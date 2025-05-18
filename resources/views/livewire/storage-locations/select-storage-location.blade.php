<div>
    @php
        use App\Models\StorageLocation;
        use Illuminate\Database\Eloquent\Collection;
        use Portavice\Bladestrap\Support\ValueHelper;

        /** @var Collection<int, StorageLocation> $allowedRootStorageLocations */
        /** @var string $id */
        /** @var string $name */
        /** @var ?StorageLocation $selectedStorageLocation */
        $oldValueForName = old(ValueHelper::nameToDotSyntax($name));
        if (isset($oldValueForName)) {
            // Overwrite selected storage location for old value.
            $selectedStorageLocation = StorageLocation::query()->find((int) $oldValueForName);
        }
    @endphp
    <i class="fa fa-fw fa-warehouse"></i>
    @isset($selectedStorageLocation)
        @can('view', $selectedStorageLocation)
            <a href="{{ $selectedStorageLocation->getRoute() }}" class="fw-bold">{{ $selectedStorageLocation->name }}</a>
        @else
            <strong>{{ $selectedStorageLocation->name }}</strong>
        @endcan
    @else
        {{ __('no storage location selected') }}
    @endisset
    <x-bs::modal.button :modal="$id . '-modal'"
                        variant="primary"
                        class="btn-sm ms-2">
        <i class="fa fa-fw fa-rotate"></i> {{ __('Change') }}
    </x-bs::modal.button>
    <x-bs::modal :id="$id . '-modal'"
                 class="modal-xl"
                 :close-button-title="__('Select storage location')"
                 wire:ignore.self>
        <x-slot:title container="h3">{{ __('Select storage location') }}</x-slot:title>
        @php
            $selectableStorageLocationsAtLevel = $allowedRootStorageLocations;
            $currentLevel = 0;
            $storageLocationAtCurrentLevel = $selectedPath[0] ?? null;

            $optionsForRoot = \Portavice\Bladestrap\Support\Options::fromModels($selectableStorageLocationsAtLevel, 'name');
            if ($selectedStorageLocation === null) {
                $optionsForRoot->prepend(__('Select storage location'), '');
            }
        @endphp
        <x-bs::form.field :name="$name . '_level0'"
                          :class="$errors->has($name) ? 'is-invalid' : ''"
                          type="select"
                          :options="$optionsForRoot"
                          :value="$storageLocationAtCurrentLevel?->id"
                          wire:change="selectStorageLocation(0, $event.target.value)">
            {{ __('Storage location') }}
            <x-slot:appendText :container="false">
                @while(isset($storageLocationAtCurrentLevel) && $storageLocationAtCurrentLevel->childStorageLocations->isNotEmpty())
                    @php
                        $selectableStorageLocationsAtLevel = $storageLocationAtCurrentLevel->childStorageLocations;
                        $currentLevel++;
                        $storageLocationAtCurrentLevel = $selectedPath[$currentLevel] ?? null;
                    @endphp
                    @if(count($selectableStorageLocationsAtLevel) > 0)
                        <x-bs::form.field :nested-in-group="true"
                                          :name="$name . '_level' . $currentLevel"
                                          type="select"
                                          :options="\Portavice\Bladestrap\Support\Options::fromModels($selectableStorageLocationsAtLevel ?? [], 'name')->prepend(__('none'), '')"
                                          :value="$storageLocationAtCurrentLevel?->id"
                                          wire:change="selectStorageLocation({{ $currentLevel }}, $event.target.value)">
                        </x-bs::form.field>
                    @endif
                @endwhile
                <x-bs::form.feedback :name="$name"/>
            </x-slot:appendText>
        </x-bs::form.field>
    </x-bs::modal>
    <input type="hidden" name="{{ $name }}" value="{{ $selectedStorageLocation->id ?? null }}">
</div>
