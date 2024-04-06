@extends('layouts.app')

@php
    /** @var \App\Models\Document $document */
@endphp

@section('title')
    {{ $document->title }}
@endsection

@section('breadcrumbs')
    @include('documents.shared.document_breadcrumbs')
    <x-bs::breadcrumb.item>{{ $document->title }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1><i class="{{ $document->file_type->getIconClass() }}" title="{{ $document->file_type->getTranslatedName() }}"></i> @yield('title')</h1>
@endsection

@section('headline-buttons')
    @can('update', $document)
        <x-button.edit href="{{ route('documents.edit', $document) }}"/>
    @endcan
    @include('documents.shared.document_delete_modal_button')
@endsection

@section('content')
    @include('documents.shared.document_delete_modal')

    <div class="my-5">
        @include('documents.shared.document_embed')
    </div>

    <x-text.timestamp :model="$document"/>
@endsection
