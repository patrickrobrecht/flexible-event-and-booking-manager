@can('view', $document->uploadedByUser)
    {!! __('File uploaded by :name', [
        'name' => sprintf(
            '<a class="%s" href="%s">%s</a>',
            $class ?? '',
            route('users.show', $document->uploadedByUser),
            $document->uploadedByUser->name
        ),
    ]) !!}
@elsecan('update', $document->uploadedByUser)
    {!! __('File uploaded by :name', [
        'name' => sprintf(
            '<a class="%s" href="%s">%s</a>',
            $class ?? '',
            route('users.edit', $document->uploadedByUser),
            $document->uploadedByUser->name
        ),
    ]) !!}
@else
    {{ __('File uploaded by :name', [
        'name' => $document->uploadedByUser->name,
    ]) }}
@endcan
