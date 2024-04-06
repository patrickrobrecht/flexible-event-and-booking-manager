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
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field id="search" name="filter[search]" type="text"
                                  :from-query="true">{{ __('Search term') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field id="file_type" name="filter[file_type]" type="select"
                                  :options="\App\Options\FileType::toOptionsWithAll()"
                                  :from-query="true"><i class="fa fa-fw fa-file-circle-question"></i> {{ __('File type') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field id="approval_status" name="filter[approval_status]" type="select"
                                  :options="\App\Options\ApprovalStatus::toOptionsWithAll()"
                                  :from-query="true"><i class="fa fa-fw fa-circle-question"></i> {{ __('Approval status') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Document::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
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
                            <i class="{{ $document->file_type->getIconClass() }} text-primary" title="{{ $document->file_type->getTranslatedName() }}"></i>
                            @can('view', $document)
                                <a href="{{ route('documents.show', $document) }}">{{ $document->title }}</a>
                            @else
                                {{ $document->title }}
                            @endcan
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        @isset($document->description)
                            <x-bs::list.item class="text-muted">{{ $document->description }}</x-bs::list.item>
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
                        <x-bs::list.item>
                            <x-badge.approval-status :approval-status="$document->approval_status"/>
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
