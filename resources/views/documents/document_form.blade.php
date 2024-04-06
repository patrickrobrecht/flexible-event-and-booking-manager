@extends('layouts.app')

@php
    /** @var \App\Models\Document $document */
@endphp

@section('title')
    {{ __('Edit :name', ['name' => $document->title]) }}
@endsection

@section('breadcrumbs')
    @include('documents.shared.document_breadcrumbs')
    @can('view', $document)
        <x-bs::breadcrumb.item href="{{ route('documents.show', $document) }}">{{ $document->title }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $document->title }}</x-bs::breadcrumb.item>
    @endif
@endsection

@section('headline')
    <h1><i class="{{ $document->file_type->getIconClass() }}" title="{{ $document->file_type->getTranslatedName() }}"></i> @yield('title')</h1>
@endsection

@section('headline-buttons')
    @include('documents.shared.document_delete_modal_button')
@endsection

@section('content')
    @include('documents.shared.document_delete_modal')

    <x-bs::form method="PUT" action="{{ route('documents.update', $document) }}"
                enctype="multipart/form-data">
        @include('documents.shared.document_form_fields', [
            'document' => $document,
        ])
        <x-bs::button.group>
            <x-button.save>{{ __( 'Save' ) }}</x-button.save>
            <x-button.cancel href="{{ route('events.show', $document->reference->getRoute()) }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <div class="my-5">
        @include('documents.shared.document_embed')
    </div>

    <x-text.timestamp :model="$document"/>
@endsection
