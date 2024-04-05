@can('download', $document)
    <x-bs::button.link variant="secondary" href="{{ route('documents.download', $document) }}">
        <i class="fa fa-fw fa-download"></i> {{ __('Download file') }}
    </x-bs::button.link>
@endcan
