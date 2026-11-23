@extends('layouts.app')

@section('title')
    {{ __('API documentation') }}
@endsection

@section('main')
    <rapi-doc spec-url="{{ route('api-docs.spec') }}"
              color-scheme="system"
              default-schema-tab="schema"
              show-header="false"
              show-method-in-nav-bar="as-colored-block"
              show-curl-before-try="true"
              allow-spec-file-download="true">
    </rapi-doc>
@endsection

@push('scripts')
    @vite(['node_modules/rapidoc/dist/rapidoc-min.js'])
@endpush
