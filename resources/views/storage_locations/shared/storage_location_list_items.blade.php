@php
    use App\Models\StorageLocation;
    use Illuminate\Database\Eloquent\Collection;

    /** @var Collection<StorageLocation> $storageLocations */

    /* @var int $marginLevel */
    $headlineLevel = $marginLevel + 2;
    $headlineTag = $headlineLevel <= 6 ? "h{$headlineLevel}" : 'strong';
@endphp

@foreach($storageLocations as $storageLocation)
    <x-bs::list.item>
        <div class="ms-large-{{$marginLevel}} d-flex justify-content-between align-items-start">
            <{{$headlineTag}}>
                @can('view', $storageLocation)
                    <a href="{{ route('storage-locations.show', $storageLocation) }}">{{ $storageLocation->name }}</a>
                @else
                    {{ $storageLocation->name }}
                @endcan
            </{{$headlineTag}}>
            @canany(['update', 'forceDelete'], $storageLocation)
                <div class="text-end">
                    @can('update', $storageLocation)
                        <x-button.edit href="{{ route('storage-locations.edit', $storageLocation) }}"/>
                    @endcan
                    @can('forceDelete', $storageLocation)
                        <x-form.delete-modal :id="$storageLocation->id"
                                             :name="$storageLocation->name"
                                             :route="route('storage-locations.destroy', $storageLocation)"/>
                    @endcan
                </div>
            @endcanany
        </div>
        <div class="ms-large-{{$marginLevel}} d-flex justify-content-between align-items-end">
            <div>
                @if($storageLocation->materials->isNotEmpty())
                    <x-bs::badge>{{ formatTransChoice(':count materials', $storageLocation->materials->count()) }}</x-bs::badge>
                    @foreach($storageLocation->material_statuses as $materialStatus)
                        <x-badge.enum :case="$materialStatus"/>
                    @endforeach
                @endif
            </div>
            <x-text.updated-human-diff :model="$storageLocation"/>
        </div>
        @isset($storageLocation->description)
            <div class="small">{{ $storageLocation->description }}</div>
        @endisset
    </x-bs::list.item>
    @include('storage_locations.shared.storage_location_list_items', [
        'storageLocations' => $storageLocation->childStorageLocations,
        'marginLevel' => $marginLevel + 1,
    ])
@endforeach
