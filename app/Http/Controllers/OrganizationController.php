<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(OrganizationFilterRequest $request): View
    {
        $this->authorize('viewAny', Organization::class);

        return view('organizations.organization_index', $this->formValues([
            'organizations' => Organization::filter()
                ->with([
                    'location',
                ])
                ->withCount([
                    'events',
                ])
                ->paginate(),
        ]));
    }

    public function create(): View
    {
        $this->authorize('create', Organization::class);

        return view('organizations.organization_form', $this->formValues());
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $organization = new Organization();
        if ($organization->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('organizations.edit', $organization));
        }

        return back();
    }

    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        return view('organizations.organization_form', $this->formValues([
            'organization' => $organization,
        ]));
    }

    public function update(Organization $organization, OrganizationRequest $request): RedirectResponse
    {
        $this->authorize('update', $organization);

        if ($organization->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'locations' => Location::query()
                ->orderBy('name')
                ->get(),
        ], $values);
    }
}
