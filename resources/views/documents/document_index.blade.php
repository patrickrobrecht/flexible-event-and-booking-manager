@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Document[] $documents */
@endphp

@section('title')
    {{ __('Documents') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>{{ __('Documents') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field id="name" name="filter[title]" type="text"
                                  :from-query="true">{{ __('Title') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field id="file_type" name="filter[file_type]" type="select"
                                  :options="\App\Options\FileType::toOptionsWithAll()"
                                  :from-query="true">{{ __('File type') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Document::sortOptions()->getNamesWithLabels()"
                                  :from-query="true">{{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$documents->total()"/>

    <div class="row my-3">
        @foreach($documents as $document)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="{{ $document->file_type->getIconClass() }} text-primary"></i>
                            @can('view', $document)
                                <a href="{{ route('documents.show', $document) }}">{{ $document->title }}</a>
                            @else
                                {{ $document->title }}
                            @endcan
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        @isset($document->description)
                            <x-bs::list.item>{{ $document->description }}</x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            @switch($document->reference::class)
                                @case(\App\Models\Event::class)
                                    <i class="fa fa-fw fa-calendar-days"></i>
                                    @can('view', $document->reference)
                                        <a href="{{ route('events.show', $document->reference) }}">{{ $document->reference->name }}</a>
                                    @else
                                        {{ $document->reference->name }}
                                    @endcan
                                    @break
                                @case(\App\Models\EventSeries::class)
                                    <i class="fa fa-fw fa-calendar-week"></i>
                                    @can('view', $document->reference)
                                        <a href="{{ route('event-series.show', $document->reference) }}">{{ $document->reference->name }}</a>
                                    @else
                                        {{ $document->reference->name }}
                                    @endcan
                                    @break
                                @case(\App\Models\Organization::class)
                                    <i class="fa fa-fw fa-sitemap"></i>
                                    @can('view', $document->reference)
                                        <a href="{{ route('organizations.show', $document->reference) }}">{{ $document->reference->name }}</a>
                                    @else
                                        {{ $document->reference->name }}
                                    @endcan
                                    @break
                            @endswitch
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-user"></i>
                            @include('documents.shared.document_uploaded_by')
                        </x-bs::list.item>
                    </x-bs::list>
                    @canany(['download', 'update'], $document)
                        <div class="card-body">
                            <x-bs::button.group>
                                @include('documents.shared.document_download_link')
                                @can('update', $document)
                                    <x-button.edit href="{{ route('documents.edit', $document) }}"/>
                                @endcan
                                @include('documents.shared.document_delete_modal_button')
                            </x-bs::button.group>
                            @include('documents.shared.document_delete_modal')
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$document"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $documents->withQueryString()->links() }}
@endsection