@php
    use App\Livewire\Materials\MaterialSearch;
    use App\Models\Material;
    use Illuminate\Database\Eloquent\Collection;
    use Portavice\Bladestrap\Support\Options;

    /** @var string $search */
    /** @var null|Collection<int, Material> $materials */
@endphp
<div>
    <div class="row">
        <div class="col-12 col-lg-8 col-xl-9">
            <x-bs::form.field name="search" type="text" maxlength="255"
                              wire:model.live.debounce.50ms="search">
                {{ __('Search term') }}
                <x-slot:hint class="fw-bold">
                    <span @class([
                        'text-danger' => strlen($search) <= MaterialSearch::MINIMUM_CHARACTERS,
                    ])>{{ formatTransChoice('Please enter at least :count characters.', MaterialSearch::MINIMUM_CHARACTERS) }}</span>
                    {{ __('Use -keyword to exclude everything containing "keyword".') }}
                </x-slot:hint>
            </x-bs::form.field>
        </div>
        <div class="col-12 col-lg-4 col-xl-3">
            <x-bs::form.field name="organization_id"
                              type="select" :options="Options::fromModels($organizations, 'name')->prepend(__('all'), \App\Enums\FilterValue::All->value)"
                              wire:model.live.debounce.50ms="organization_id"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
        </div>
    </div>

    @isset($materials)
        <x-alert.count class="mt-3" :count="$materials->count()"/>

        <x-bs::list>
            @foreach($materials as $material)
                @if($material->storageLocations->isEmpty())
                    <x-bs::list.item :variant="$loop->even ? 'secondary' : null">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                @include('livewire.materials.material-row')
                            </div>
                        </div>
                    </x-bs::list.item>
                @else
                    @foreach($material->storageLocations as $storageLocation)
                        <x-bs::list.item :variant="$loop->parent->even ? 'secondary' : null">
                            <div class="row">
                                @if($loop->first)
                                    <div class="col-12 col-md-6">
                                        @include('livewire.materials.material-row')
                                    </div>
                                @endif
                                <div @class([
                                    'col-11 offset-1 col-md-6',
                                    $loop->first ? 'offset-md-0' : 'offset-md-6',
                                ])>
                                    <span>
                                        <i class="fa fa-fw fa-warehouse"></i>
                                        @foreach($storageLocation->getAncestors() as $storageLocationAncestor)
                                            @can('view', $storageLocationAncestor)
                                                <a href="{{ $storageLocationAncestor->getRoute() }}">{{ $storageLocationAncestor->name }}</a>
                                            @else
                                                <strong>{{ $storageLocationAncestor->name }}</strong>
                                            @endcan
                                            •
                                        @endforeach
                                        @can('view', $storageLocation)
                                            <a href="{{ $storageLocation->getRoute() }}" class="fw-bold">{{ $storageLocation->name }}</a>
                                        @else
                                            <strong>{{ $storageLocation->name }}</strong>
                                        @endcan
                                    </span>
                                    <div class="small">
                                        <x-badge.enum :case="$storageLocation->pivot->material_status"/>
                                        <span class="ms-2">{{ __('Stock') }}: {{ isset($storageLocation->pivot->stock) ? formatInt($storageLocation->pivot->stock) : __('unknown') }}</span>
                                        @isset($storageLocation->pivot->remarks)
                                            • {{ $storageLocation->pivot->remarks }}
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </x-bs::list.item>
                    @endforeach
                @endif
            @endforeach
        </x-bs::list>
    @endif
</div>
