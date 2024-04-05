@can('viewAny', \App\Models\Document::class)
    <x-bs::breadcrumb.item href="{{ route('organizations.index') }}">{{ __('Organizations') }}</x-bs::breadcrumb.item>
@else
    <x-bs::breadcrumb.item>{{ __('Organizations') }}</x-bs::breadcrumb.item>
@endcan
@isset($organization)
    @can('view', $organization)
        <x-bs::breadcrumb.item href="{{ route('organizations.show', $organization) }}">{{ $organization->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $organization->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
