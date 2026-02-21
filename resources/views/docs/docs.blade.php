@extends('layouts.app')

@section('title')
    {{ __('API documentation') }}
@endsection

@section('main')
    <div id="swagger-ui" data-spec-url="{{ route('api-docs.spec') }}"></div>
@endsection

@push('scripts')
    <script src="{{ Vite::asset('node_modules/swagger-ui-dist/swagger-ui-bundle.js') }}"></script>
    @vite([
        'resources/js/swagger-ui.js',
        'node_modules/swagger-ui-dist/swagger-ui.css',
    ])
@endpush
