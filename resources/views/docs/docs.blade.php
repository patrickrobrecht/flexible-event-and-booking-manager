@extends('layouts.app')

@section('title')
    {{ __('API documentation') }}
@endsection

@section('main')
    <rapi-doc spec-url="{{ route('api-docs.spec') }}"
              color-scheme="system"
              layout="column"
              render-style="focused"
              default-schema-tab="schema"
              show-header="false"
              show-method-in-nav-bar="as-colored-block"
              use-path-in-nav-bar="true">
    </rapi-doc>
@endsection

@push('scripts')
    @vite(['node_modules/rapidoc/dist/rapidoc-min.js'])
@endpush
