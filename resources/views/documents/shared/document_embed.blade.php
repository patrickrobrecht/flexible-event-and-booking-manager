@php
    /** @var \App\Models\Document $document */
@endphp
@switch($document->file_type)
    @case(\App\Enums\FileType::Audio)
        <audio controls>
            <source src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}"/>
        </audio>
        @break
    @case(\App\Enums\FileType::Image)
        <img src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}" class="img-fluid"/>
        @break
    @case(\App\Enums\FileType::PDF)
        <object data="{{ route('documents.stream', $document) }}" type="application/pdf" class="w-100 vh-100"></object>
        @break
    @case(\App\Enums\FileType::Video)
        <video width="1000" controls>
            <source src="{{ route('documents.stream', $document) }}" alt="{{ $document->title }}"/>
        </video>
        @break
    @default
        <x-bs::alert variant="danger">{{ __('Unfortunately, it is not possible to display this document in the browser. However, you can download the file.') }}</x-bs::alert>
        @break
@endswitch

<div class="mt-3">
    <x-bs::button.link variant="secondary" href="{{ route('documents.download', $document) }}" class="text-nowrap">
        <i class="fa fa-fw fa-download"></i> {{ __('Download file') }}
    </x-bs::button.link>
</div>
