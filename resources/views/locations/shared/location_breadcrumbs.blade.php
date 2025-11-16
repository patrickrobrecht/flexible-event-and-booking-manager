@can('viewAny', \App\Models\Location::class)
    <x-bs::breadcrumb.item href="{{ route('locations.index') }}">{{ __('Locations') }}</x-bs::breadcrumb.item>
@else
    <x-bs::breadcrumb.item>{{ __('Locations') }}</x-bs::breadcrumb.item>
@endcan
@isset($location)
    @can('view', $location)
        <x-bs::breadcrumb.item href="{{ route('locations.show', $location) }}">{{ $location->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $location->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
