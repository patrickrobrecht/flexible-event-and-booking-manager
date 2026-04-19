<?php

namespace App\Http\Controllers;

use App\Exports\StorageLocationsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\Filters\StorageLocationFilterRequest;
use App\Http\Requests\StorageLocationRequest;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageLocationController extends Controller
{
    use StreamsExport;

    public function index(StorageLocationFilterRequest $request): StreamedResponse|View
    {
        $this->authorize('viewAny', StorageLocation::class);
        ValueHelper::setDefaults(StorageLocation::defaultValuesForQuery());

        /** @var Builder<StorageLocation> $storageLocationsQuery */
        $storageLocationsQuery = StorageLocation::buildQueryFromRequest()
            ->with(StorageLocation::relationsForChildStorageLocationsAndMaterial());

        $output = $request->query('output');
        if ($output === 'export') {
            return $this->streamExcelExport(
                new StorageLocationsExportSpreadsheet(
                    $storageLocationsQuery
                        ->whereNull('parent_storage_location_id')
                        ->with([
                            'parentStorageLocation',
                            'materials',
                        ])
                        ->get()
                ),
                __('Storage locations') . '.xlsx',
            );
        }

        return view('storage_locations.storage_location_index', [
            'storageLocations' => $storageLocationsQuery
                ->withCount([
                    'materials',
                ])
                ->paginate(ValueHelper::getFromQueryOrDefault('sort') === StorageLocation::HIERARCHICAL ? 5 : 20),
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
