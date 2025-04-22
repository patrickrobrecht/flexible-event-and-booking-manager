<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class LocationController extends Controller
{
    public function index(LocationFilterRequest $request): View
    {
        $this->authorize('viewAny', Location::class);
        ValueHelper::setDefaults(Location::defaultValuesForQuery());

        return view('locations.location_index', [
            'locations' => Location::buildQueryFromRequest()
                ->withCount([
                    'events',
                    'organizations',
                ])
                ->paginate(18),
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Location::class);

        return view('locations.location_form');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $this->authorize('create', Location::class);

        $location = new Location();
        if ($location->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $location->nameOrAddress]));
            return redirect(route('locations.edit', $location));
        }

        return back();
    }

    public function edit(Location $location): View
    {
        $this->authorize('update', $location);

        return view('locations.location_form', [
            'location' => $location,
        ]);
    }

    public function update(Location $location, LocationRequest $request): RedirectResponse
    {
        $this->authorize('update', $location);

        if ($location->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $location->nameOrAddress]));
        }

        return back();
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('forceDelete', $location);

        if ($location->delete()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $location->nameOrAddress]));
            return redirect(route('locations.index'));
        }

        return back();
    }
}
