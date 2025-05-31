@php
    use App\Models\Material;

    /** @var Material $material */
    /** @var string $class */
@endphp
<div class="row">
    <div class="col-12 col-xxl-8">
        <i class="fa fa-fw fa-toolbox"></i>
        @can('view', $material)
            <a href="{{ $material->getRoute() }}" class="fw-bold">{{ $material->name }}</a>
        @else
            <strong>{{ $material->name }}</strong>
        @endcan
        @isset($material->description)
            <div class="small">{{ $material->description }}</div>
        @endisset
    </div>
    <div class="col-12 col-xxl-4">
        <i class="fa fa-fw fa-sitemap"></i>
        @can('view', $material->organization)
            <a href="{{ $material->organization->getRoute() }}">{{ $material->organization->name }}</a>
        @else
            <strong>{{ $material->organization->name }}</strong>
        @endcan
    </div>
</div>
