@extends('layouts.app')

@php
    use App\Models\Material;
    use App\Models\Organization;
    use Illuminate\Database\Eloquent\Collection;
    use Portavice\Bladestrap\Support\Options;

    /** @var ?Material $material */
    /** @var Collection<Organization> $organizations */
@endphp

@section('title')
    @isset($material)
        {{ __('Edit :name', ['name' => $material->name]) }}
    @else
        {{ __('Create material') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('materials.index') }}">{{ __('Materials') }}</x-bs::breadcrumb.item>
    @isset($material)
        <x-bs::breadcrumb.item href="{{ route('materials.show', $material) }}">{{ $material->name }}</x-bs::breadcrumb.item>
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($material) ? 'PUT' : 'POST' }}"
                action="{{ isset($material) ? route('materials.update', $material) : route('materials.store') }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <x-bs::form.field name="name" type="text" :required="true"
                                  :value="$material->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="description" type="textarea"
                                  :value="$material->description ?? null">{{ __('Description') }}</x-bs::form.field>
                <x-bs::form.field name="organization_id" type="radio" :options="Options::fromModels($organizations, 'name')" :required="true"
                                  :value="$material->organization_id ?? null"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <h2>{{ __('Storage locations') }}</h2>
                <x-bs::list>
                    @isset($material)
                        @foreach($material->storageLocations as $storageLocation)
                            <x-bs::list.item>
                                @livewire('storage-locations.select-storage-location', [
                                    'id' => 'storage-location-' . $storageLocation->pivot->id,
                                    'name' => 'storage_locations[' . $storageLocation->pivot->id . '][storage_location_id]',
                                    'selectedStorageLocation' => $storageLocation,
                                ], key('storage-location-' . $storageLocation->pivot->id))
                                <x-bs::form.field name="storage_locations[{{ $storageLocation->pivot->id }}][remove]"
                                                  type="checkbox" :options="Options::one(__('Remove storage location'))"/>
                                <div class="row mt-3">
                                    <div class="col-12 col-xxl-6">
                                        <x-bs::form.field name="storage_locations[{{ $storageLocation->pivot->id }}][material_status]"
                                                          type="select" :options="\App\Enums\MaterialStatus::toOptions()"
                                                          :required="true"
                                                          :value="$storageLocation->pivot->material_status">
                                            {{ __('Status') }}
                                        </x-bs::form.field>
                                    </div>
                                    <div class="col-12 col-xxl-6">
                                        <x-bs::form.field name="storage_locations[{{ $storageLocation->pivot->id }}][stock]"
                                                          type="number" min="1" step="1"
                                                          :value="$storageLocation->pivot->stock">
                                            {{ __('Stock') }}
                                        </x-bs::form.field>
                                    </div>
                                </div>
                                <x-bs::form.field name="storage_locations[{{ $storageLocation->pivot->id }}][remarks]" type="textarea"
                                                  :value="$storageLocation->pivot->remarks">
                                    {{ __('Remarks') }}
                                </x-bs::form.field>
                            </x-bs::list.item>
                        @endforeach
                    @endisset
                    <x-bs::list.item>
                        <div class="small mb-1">{{ __('To add another storage location, select one.') }}</div>
                        @php
                            $selectedStorageLocation = null;
                            if (!isset($selectedStorageLocation)) {
                                $selectedStorageLocationIdFromQuery = \Portavice\Bladestrap\Support\ValueHelper::getFromQueryOrDefault('storage_location_id');
                                if (isset($selectedStorageLocationIdFromQuery)) {
                                    $selectedStorageLocation = \App\Models\StorageLocation::query()->find($selectedStorageLocationIdFromQuery);
                                }
                            }
                        @endphp
                        @livewire('storage-locations.select-storage-location', [
                            'id' => 'storage-location-new',
                            'name' => 'storage_locations[new][storage_location_id]',
                            'selectedStorageLocation' => $selectedStorageLocation,
                        ], key('storage-location-new'))
                        <div class="row mt-3">
                            <div class="col-12 col-xxl-6">
                                <x-bs::form.field name="storage_locations[new][material_status]"
                                                  type="select" :options="\App\Enums\MaterialStatus::toOptions()->prepend(__('select status'), '')">
                                    {{ __('Status') }}
                                </x-bs::form.field>
                            </div>
                            <div class="col-12 col-xxl-6">
                                <x-bs::form.field name="storage_locations[new][stock]"
                                                  type="number" min="1" step="1">
                                    {{ __('Stock') }}
                                </x-bs::form.field>
                            </div>
                        </div>
                        <x-bs::form.field name="storage_locations[new][remarks]" type="textarea">
                            {{ __('Remarks') }}
                        </x-bs::form.field>
                    </x-bs::list.item>
                </x-bs::list>
            </div>
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($material){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('materials.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$material ?? null"/>
@endsection
