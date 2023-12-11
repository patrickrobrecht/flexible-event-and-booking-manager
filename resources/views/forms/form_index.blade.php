@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Form[] $forms */
@endphp

@section('title')
    {{ __('Forms') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\Form::class)
            <x-button.create href="{{ route('forms.create') }}">
                {{ __('Create form') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <x-bs::form.field id="name" name="filter[name]" type="text"
                          :from-query="true">{{ __('Name') }}</x-bs::form.field>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$forms->total()"/>

    <div class="row my-3">
        @foreach($forms as $form)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $form->name }}</h2>
                    </div>
                    <div class="card-body">
                        @can('update', $form)
                            <x-button.edit href="{{ route('forms.edit', $form) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$form"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $forms->withQueryString()->links() }}
@endsection
