@php
    /** @var \App\Models\Document $document */
@endphp
@switch($document->file_type)
    @case(\App\Options\FileType::Audio)
        <audio controls>
            <source src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}"/>
            @include('documents.shared.document_download_link')
        </audio>
        @break
    @case(\App\Options\FileType::Image)
        <img src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}"/>
        @break
    @case(\App\Options\FileType::Video)
        <video width="1000" controls>
            <source src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}"/>
            @include('documents.shared.document_download_link')
        </video>
        @break
    @default
        <x-bs::alert variant="danger">{{ __('Unfortunately, it is not possible to display this document in the browser. However, you can download the file.') }}</x-bs::alert>
        @include('documents.shared.document_download_link')
        @break
@endswitch
