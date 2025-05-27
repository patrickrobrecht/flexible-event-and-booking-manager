@extends('layouts.app')

@section('title')
    {{ __('Material search') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('materials.index') }}">{{ __('Materials') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    @livewire('materials.material-search')
@endsection
