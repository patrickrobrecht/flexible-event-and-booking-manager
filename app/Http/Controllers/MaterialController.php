<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\MaterialFilterRequest;
use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class MaterialController extends Controller
{
    public function index(MaterialFilterRequest $request): View
    {
        $this->authorize('viewAny', Material::class);
        ValueHelper::setDefaults(Material::defaultValuesForQuery());

        return view('materials.material_index', [
            ...$this->formValues(),
            'materials' => Material::buildQueryFromRequest()
                ->paginate(),
        ]);
    }

    public function search(): View
    {
        $this->authorize('viewAny', Material::class);

        return view('materials.material_search', [
            'organizations' => Organization::query()
                ->whereHas('materials')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Material::class);

        return view('materials.material_form', $this->formValues());
    }

    public function store(MaterialRequest $request): RedirectResponse
    {
        $this->authorize('create', Material::class);

        $material = new Material();
        /** @phpstan-ignore argument.type */
        if ($material->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $material->name]));
            return $this->actionAwareRedirect(
                $request,
                route('materials.show', $material),
                createRoute: route('materials.create')
            );
        }

        return back();
    }

    public function show(Material $material): View
    {
        $this->authorize('view', $material);

        return view('materials.material_show', [
            'material' => $material->loadWithStorageLocations(),
        ]);
    }

    public function edit(Material $material): View
    {
        $this->authorize('update', $material);

        return view('materials.material_form', [
            ...$this->formValues(),
            'material' => $material->loadWithStorageLocations(),
        ]);
    }

    public function update(MaterialRequest $request, Material $material): RedirectResponse
    {
        $this->authorize('update', $material);

        /** @phpstan-ignore argument.type */
        if ($material->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $material->name]));
        }

        return $this->actionAwareRedirect(
            $request,
            route('materials.show', $material),
            editRoute: route('materials.edit', $material)
        );
    }

    public function destroy(Material $material): RedirectResponse
    {
        $this->authorize('forceDelete', $material);

        if ($material->deleteAfterDetachingStorageLocations()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $material->name]));
            return redirect(route('materials.index'));
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function formValues(): array
    {
        return [
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ];
    }
}
