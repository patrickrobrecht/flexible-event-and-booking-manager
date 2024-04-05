@php
    /** @var ?\App\Models\Document $document */
@endphp
<div class="row" x-data="{ fileSize: undefined, maxFileSize: undefined, alert: false }">
    <div class="col-12 col-xl-6">
        @php
            $maxFileSizeAsText = \App\Http\Requests\DocumentRequest::getMaxFileSizeInMegaBytes() . ' MB';
        @endphp
        <x-bs::form.field name="file"
                          type="file" :accept="\App\Options\FileType::extensionsForHtmlAccept()"
                          data-max-file-size="{{ \App\Http\Requests\DocumentRequest::getMaxFileSizeInBytes() }}"
                          x-ref="file" @change="() => {
                alert = false;
                fileSize = $refs.file.files[0].size;
                maxFileSize = $refs.file.dataset.maxFileSize;
                if (!isNaN(maxFileSize) && fileSize > maxFileSize) {
                    let formatter = new Intl.NumberFormat('{{ app()->getLocale() }}', { maximumFractionDigits: 2 });
                    fileSize = formatter.format(fileSize / 1024 / 1024);
                    maxFileSize = formatter.format(maxFileSize / 1024 / 1024);
                    alert = `{{ __('The file is :actualSize, but the maximum allowed size is :allowedSize.', [
                        'actualSize' => '${fileSize} MB',
                        'allowedSize' => '${maxFileSize} MB',
                    ]) }}`;
                }

                if ($refs.title.value === '') {
                    let fileName = $refs.file.files[0].name;
                    fileName = fileName.substring(0, fileName.lastIndexOf('.'));
                    $refs.title.value = fileName.replace(/_/g, ' ');
                }
            }">
            {{ __('File') }}
            @isset($document)
                <x-slot:appendText :container="false">
                    <x-bs::button.link variant="primary" href="{{ route('documents.download', $document) }}">
                        <i class="fa fa-fw fa-download"></i> {{ __('Download file') }}
                    </x-bs::button.link>
                </x-slot:appendText>
            @endif
            <x-slot:hint>{{ __('Maximum file size') }}: {{ $maxFileSizeAsText }}</x-slot:hint>
        </x-bs::form.field>
        @isset($document)
            <div class="mt-1">
                <i class="fa fa-fw fa-user"></i>
                @include('documents.shared.document_uploaded_by')
            </div>
            <p class="strong">{{ __("If you do not upload a new file, the current file will remain.") }}</p>
        @endisset
        <x-bs::alert variant="danger" x-show="alert !== false" x-text="alert"></x-bs::alert>
    </div>
    <div class="col-12 col-xl-6">
        <x-bs::form.field name="title" type="text"
                          :value="$document->title ?? null"
                          x-ref="title">{{ __('Title') }}</x-bs::form.field>
        <x-bs::form.field name="description" type="textarea"
                          :value="$document->description ?? null">{{ __('Description') }}</x-bs::form.field>
    </div>
</div>

@push('scripts')
    <script src="{{ mix('/lib/alpinejs.min.js') }}" defer></script>
@endpush
