<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorageLocationRequest;
use App\Models\StorageLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class StorageLocationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', StorageLocation::class);
        ValueHelper::setDefaults(StorageLocation::defaultValuesForQuery());

        return view('storage_locations.storage_location_index', [
            'storageLocations' => StorageLocation::buildQueryFromRequest()
                ->whereNull('parent_storage_location_id')
                ->with(StorageLocation::relationsForChildStorageLocationsAndMaterial())
                ->withCount([
                    'materials',
                ])
                ->paginate(5),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', StorageLocation::class);

        return view('storage_locations.storage_location_form');
    }

    public function store(StorageLocationRequest $request): RedirectResponse
    {
        $this->authorize('create', StorageLocation::class);

        $storageLocation = new StorageLocation();
        /** @phpstan-ignore argument.type */
        if ($storageLocation->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $storageLocation->name]));
            return $this->actionAwareRedirect(
                $request,
                route('storage-locations.show', $storageLocation),
                createRoute: route('storage-locations.create')
            );
        }

        return back();
    }

    public function show(StorageLocation $storageLocation): View
    {
        $this->authorize('view', $storageLocation);

        return view('storage_locations.storage_location_show', [
            'storageLocation' => $storageLocation->load(StorageLocation::relationsForChildStorageLocationsAndMaterial()),
        ]);
    }

    public function edit(StorageLocation $storageLocation): View
    {
        $this->authorize('update', $storageLocation);

        return view('storage_locations.storage_location_form', [
            'storageLocation' => $storageLocation,
        ]);
    }

    public function update(StorageLocationRequest $request, StorageLocation $storageLocation): RedirectResponse
    {
        $this->authorize('update', $storageLocation);

        /** @phpstan-ignore argument.type */
        if ($storageLocation->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $storageLocation->name]));
        }

        return $this->actionAwareRedirect(
            $request,
            route('storage-locations.show', $storageLocation),
            editRoute: route('storage-locations.edit', $storageLocation)
        );
    }

    public function destroy(StorageLocation $storageLocation): RedirectResponse
    {
        $this->authorize('forceDelete', $storageLocation);

        if ($storageLocation->delete() === true) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $storageLocation->name]));
            return redirect(route('storage-locations.index'));
        }

        return back();
    }
}
