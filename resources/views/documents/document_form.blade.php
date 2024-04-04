@extends('layouts.app')

@php
    /** @var \App\Models\Document $document */
@endphp

@section('title')
    {{ __('Edit :name', ['name' => $document->title]) }}
@endsection

@section('breadcrumbs')
    @if($document->reference::class === \App\Models\Event::class)
        @php
            $event = $document->reference;
            $parentEvent = $event->parentEvent ?? null;
        @endphp
        <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
        @isset($parentEvent)
            <x-bs::breadcrumb.item href="{{ route('events.show', $parentEvent) }}">{{ $parentEvent->name }}</x-bs::breadcrumb.item>
        @endisset
        @isset($event)
            <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
        @endisset
    @endif
    <x-bs::breadcrumb.item>{{ __('Documents') }}</x-bs::breadcrumb.item>
    @can('download', $document)
        <x-bs::breadcrumb.item href="{{ route('documents.download', $document) }}">{{ $document->title }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $document->title }}</x-bs::breadcrumb.item>
    @endif
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

    <x-text.timestamp :model="$document"/>
@endsection
