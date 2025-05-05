<div>
    @php
        $selectableStorageLocationsAtLevel = $allowedRootStorageLocations
            ->when($storageLocation, fn ($collection) => $collection->where('id', '!=', $storageLocation?->id));
        $currentLevel = 0;
        $storageLocationAtCurrentLevel = $selectedPath[0] ?? null;
    @endphp
    <x-bs::form.field name="parent_storage_location_id_level0"
                      :class="$errors->has('parent_storage_location_id') ? 'is-invalid' : ''"
                      type="select"
                      :options="\Portavice\Bladestrap\Support\Options::fromModels($selectableStorageLocationsAtLevel, 'name')->prepend(__('none'), '')"
                      :value="$storageLocationAtCurrentLevel?->id"
                      wire:change="selectStorageLocation(0, $event.target.value)">
        {{ __('Parent storage location') }}
        <x-slot:appendText :container="false">
            @while(isset($storageLocationAtCurrentLevel) && $storageLocationAtCurrentLevel->childStorageLocations->isNotEmpty())
                @php
                    $selectableStorageLocationsAtLevel = $storageLocationAtCurrentLevel->childStorageLocations
                        ->when($storageLocation, fn ($collection) => $collection->where('id', '!=', $storageLocation?->id));

                    $currentLevel++;
                    $storageLocationAtCurrentLevel = $selectedPath[$currentLevel] ?? null;
                @endphp
                @if(count($selectableStorageLocationsAtLevel) > 0)
                    <x-bs::form.field :nested-in-group="true"
                                      :name="'parent_storage_location_id_level' . $currentLevel"
                                      type="select"
                                      :options="\Portavice\Bladestrap\Support\Options::fromModels($selectableStorageLocationsAtLevel ?? [], 'name')->prepend(__('none'), '')"
                                      :value="$storageLocationAtCurrentLevel?->id"
                                      wire:change="selectStorageLocation({{ $currentLevel }}, $event.target.value)">
                    </x-bs::form.field>
                @endif
            @endwhile
            <x-bs::form.feedback name="parent_storage_location_id"/>
        </x-slot:appendText>
    </x-bs::form.field>

    <input type="hidden" name="parent_storage_location_id" value="{{ $selectedStorageLocation->id ?? null }}">
</div>
