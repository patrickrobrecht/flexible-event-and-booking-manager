<?php

namespace App\Livewire\Materials;

use App\Models\Material;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class MaterialSearch extends Component
{
    public const int MINIMUM_CHARACTERS = 3;

    #[Url(as: 'term', history: true, keep: true)]
    public string $search = '';

    public function render(): View
    {
        if (strlen($this->search) >= self::MINIMUM_CHARACTERS) {
            $searchTerms = explode(',', $this->search);

            $materials = Material::query()
                /** @see Material::scopeNameAndDescription() */
                ->nameAndDescription(...$searchTerms)
                ->with([
                    'organization',
                    'storageLocations',
                ])
                ->orderBy('name')
                ->get();
        }

        return view('livewire.materials.material-search', [
            'materials' => $materials ?? null,
        ]);
    }
}
