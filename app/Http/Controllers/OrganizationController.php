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

        return view('organizations.organization_index', [
            ...$this->formValues(),
            'organizations' => Organization::buildQueryFromRequest()
                ->with([
                    'location',
                    'responsibleUsers',
                ])
                ->withCount([
                    'documents',
                    'events',
                    'eventSeries',
                    'materials',
                ])
                ->paginate(10),
        ]);
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
        /** @phpstan-ignore argument.type */
        if ($organization->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $organization->name]));
            return $this->actionAwareRedirect(
                $request,
                route('organizations.show', $organization),
                createRoute: route('organizations.create')
            );
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

        return view('organizations.organization_form', [
            ...$this->formValues(),
            'organization' => $organization,
        ]);
    }

    public function update(Organization $organization, OrganizationRequest $request): RedirectResponse
    {
        $this->authorize('update', $organization);

        /** @phpstan-ignore argument.type */
        if ($organization->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $organization->name]));
        }

        // Slug may have changed, so we need to generate the URL here!
        return $this->actionAwareRedirect(
            $request,
            route('organizations.show', $organization),
            editRoute: route('organizations.edit', $organization)
        );
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('forceDelete', $organization);

        if ($organization->delete()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $organization->name]));
            return redirect(route('organizations.index'));
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function formValues(): array
    {
        return [
            'locations' => Location::query()
                ->orderBy('name')
                ->get(),
        ];
    }
}
