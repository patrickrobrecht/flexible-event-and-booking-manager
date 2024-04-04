@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents */
@endphp

@if($documents->count() > 0)
    <x-bs::list>
        @foreach($documents as $document)
            <x-bs::list.item>
                <div class="fw-bold">{{ $document->title }}</div>
                @isset($document->description)
                    <div class="text-muted">{{ $document->description }}</div>
                @endisset
                @canany(['download', 'update', 'forceDelete'], $document)
                    <x-bs::button.group class="mt-3">
                        @can('download', $document)
                            <x-bs::button.link variant="secondary" href="{{ route('documents.download', $document) }}">
                                <i class="fa fa-fw fa-download"></i> {{ __('Download file') }}
                            </x-bs::button.link>
                        @endcan
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
