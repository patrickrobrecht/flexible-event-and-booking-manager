<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class OrganizationController extends Controller
{
    public function index(OrganizationFilterRequest $request): View
    {
        $this->authorize('viewAny', Organization::class);
        ValueHelper::setDefaults(Organization::defaultValuesForQuery());

        return view('organizations.organization_index', $this->formValues([
            'organizations' => Organization::buildQueryFromRequest()
                ->with([
                    'location',
                    'responsibleUsers',
                ])
                ->withCount([
                    'documents',
                    'events',
                    'eventSeries',
                ])
                ->paginate(10),
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

    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);

        return view('organizations.organization_show', [
            'organization' => $organization->loadMissing([
                'documents.reference',
                'documents.uploadedByUser',
            ]),
        ]);
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

        // Slug may have changed, so we need to generate the URL here!
        return redirect(route('organizations.edit', $organization));
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
