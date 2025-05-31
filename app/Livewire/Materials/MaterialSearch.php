<?php

namespace App\Livewire\Materials;

use App\Enums\FilterValue;
use App\Models\Material;
use App\Models\Organization;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

class MaterialSearch extends Component
{
    public const int MINIMUM_CHARACTERS = 3;

    #[Url(as: 'term', history: true)]
    public string $search = '';

    #[Url(as: 'organization_id', history: true)]
    public int|string $organization_id = FilterValue::All->value;

    /** @var Collection<int, Organization> */
    #[Locked]
    public Collection $organizations;

    /**
     * @param Collection<int, Organization> $organizations
     */
    public function mount(Collection $organizations): void
    {
        $this->organizations = $organizations;
    }

    public function render(): View
    {
        if (strlen($this->search) >= self::MINIMUM_CHARACTERS) {
            $searchTerms = explode(',', $this->search);

            $materialQuery = Material::query()
                /** @see Material::scopeNameAndDescription() */
                ->nameAndDescription(...$searchTerms);
            if (isset($this->organization_id) && $this->organization_id !== FilterValue::All->value) {
                $materialQuery->where('organization_id', '=', $this->organization_id);
            }
            $materials = $materialQuery
                ->with([
                    'organization',
                    'storageLocations' . str_repeat('.parentStorageLocation', StorageLocation::MAX_CHILD_LEVELS),
                ])
                ->orderBy('name')
                ->get();
        }

        return view('livewire.materials.material-search', [
            'materials' => $materials ?? null,
        ]);
    }
}
