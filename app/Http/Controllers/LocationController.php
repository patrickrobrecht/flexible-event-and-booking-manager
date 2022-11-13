<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(LocationFilterRequest $request): View
    {
        $this->authorize('viewAny', Location::class);

        return view('locations.location_index', [
            'locations' => Location::filter()
                ->withCount([
                    'events',
                    'organizations',
                ])
                ->paginate(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Location::class);

        return view('locations.location_form');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $location = new Location();
        if ($location->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
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
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }
}
