@extends('layouts.app')

@section('title')
    {{ __('API documentation') }}
@endsection

@section('main')
    <rapi-doc spec-url="{{ route('api-docs.spec') }}"
              mono-font="var(--bs-font-monospace)" load-fonts="false" regular-font="var(--bs-body-font-family)"
              render-style="read" show-header="false">
    </rapi-doc>
@endsection

@push('scripts')
    @vite(['node_modules/rapidoc/dist/rapidoc-min.js'])
@endpush
