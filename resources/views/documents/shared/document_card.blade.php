@php
    use App\Models\Document;

    /** @var Document $document */
@endphp
<div class="card avoid-break">
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
                @case(\App\Models\Location::class)
                    <i class="fa fa-fw fa-location-pin"></i>
                    @can('view', $document->reference)
                        <a href="{{ route('locations.show', $document->reference) }}">{{ $document->reference->nameOrAddress }}</a>
                    @else
                        {{ $document->reference->nameOrAddress }}
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
            <i class="fa fa-fw fa-circle-question"></i>
            <x-badge.enum :case="$document->approval_status"/>
        </x-bs::list.item>
    </x-bs::list>
    @canany(['download', 'update'], $document)
        <div class="card-body d-flex flex-wrap gap-1 d-print-none">
            @include('documents.shared.document_download_link')
            @can('update', $document)
                <x-button.edit href="{{ route('documents.edit', $document) }}"/>
            @endcan
            @include('documents.shared.document_delete_modal_button')
            @include('documents.shared.document_delete_modal')
        </div>
    @endcanany
    <div class="card-footer">
        <x-text.updated-human-diff :model="$document"/>
    </div>
</div>
