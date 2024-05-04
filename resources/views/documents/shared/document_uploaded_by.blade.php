@can('view', $document->uploadedByUser)
    {!! __('File uploaded by :name', [
        'name' => sprintf(
            '<a href="%s">%s</a>',
            route('users.show', $document->uploadedByUser),
            $document->uploadedByUser->name
        ),
    ]) !!}
@elsecan('update', $document->uploadedByUser)
    {!! __('File uploaded by :name', [
        'name' => sprintf(
            '<a href="%s">%s</a>',
            route('users.edit', $document->uploadedByUser),
            $document->uploadedByUser->name
        ),
    ]) !!}
@else
    {{ __('File uploaded by :name', [
        'name' => $document->uploadedByUser->name,
    ]) }}
@endcan
