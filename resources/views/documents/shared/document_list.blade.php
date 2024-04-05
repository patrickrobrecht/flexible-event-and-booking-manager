@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents */
@endphp

@if($documents->count() > 0)
    <x-bs::list>
        @foreach($documents as $document)
            <x-bs::list.item>
                <div class="fw-bold">
                    <i class="{{ $document->file_type->getIconClass() }}"></i>
                    {{ $document->title }}
                </div>
                @isset($document->description)
                    <div class="text-muted">{{ $document->description }}</div>
                @endisset
                <div class="mt-1">
                    <i class="fa fa-fw fa-user"></i>
                    @include('documents.shared.document_uploaded_by')
                </div>
                @canany(['download', 'update', 'forceDelete'], $document)
                    <x-bs::button.group class="mt-3">
                        @include('documents.shared.document_download_link')
                        @can('update', $document)
                            <x-button.edit href="{{ route('documents.edit', $document) }}"/>
                        @endcan
                        @include('documents.shared.document_delete_modal_button')
                    </x-bs::button.group>
                    @include('documents.shared.document_delete_modal')
                @endcanany
            </x-bs::list.item>
        @endforeach
    </x-bs::list>
@endif
