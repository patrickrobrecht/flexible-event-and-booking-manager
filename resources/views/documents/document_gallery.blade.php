@extends('layouts.app')

@php
    use App\Models\Document;
    use App\Models\Traits\HasDocuments;
    use Illuminate\Database\Eloquent\Collection;

    /** @var HasDocuments $reference */
    /** @var Collection<int, Document> $images */
@endphp

@section('title')
    {{ $reference->name }} - {{ __('Image gallery') }}
@endsection

@section('breadcrumbs')
    @include('documents.shared.document_breadcrumbs')
    <x-bs::breadcrumb.item href="{{ $reference->getRoute() }}">{{ $reference->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ __('Image gallery') }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1><i class="{{ \App\Enums\FileType::Image->getIconClass() }}"></i> @yield('title')</h1>
@endsection

@section('content')
    <div id="imageGalleryCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($reference->images as $index => $image)
                <button type="button" data-bs-target="#imageGalleryCarousel" data-bs-slide-to="{{ $index }}" @if($loop->first) class="active" aria-current="true" @endif aria-label="{{ $image->title }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($reference->images as $image)
                <div @class([
                    'carousel-item',
                    'active' => $loop->first
                ])>
                    <img src="{{ route('documents.stream', $image) }}" class="d-block w-100" alt="{{ $image->title }}">
                    @if($image->title || $image->description)
                        <div class="carousel-caption d-none d-md-block">
                            <h5>{{ $image->title }}</h5>
                            <p>{{ $image->description }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#imageGalleryCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('pagination.previous') }}</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#imageGalleryCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('pagination.next') }}</span>
        </button>
    </div>

    <div class="gallery-thumbnails row mt-2">
        @foreach($reference->images as $index => $image)
            <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="card gallery-card">
                    <a data-bs-target="#imageGalleryCarousel" data-bs-slide-to="{{ $index }}">
                        <img src="{{ route('documents.stream', $image) }}" class="card-img-top" alt="{{ $image->title }}">
                    </a>
                    <div class="card-body">
                        <h2 class="card-title fw-bold small">{{ $image->title }}</h2>
                        @isset($image->description)
                            <p class="small">{{ $image->description }}</p>
                        @endisset
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
