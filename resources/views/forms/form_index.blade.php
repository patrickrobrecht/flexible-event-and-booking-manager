@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Form[] $forms */
@endphp

@section('title')
    {{ __('Forms') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\Form::class)
            <x-button.create href="{{ route('forms.create') }}">
                {{ __('Create form') }}
            </x-button.create>
        @endcan
    </x-button.group>

    <x-form.filter method="GET">
       <x-form.row>
           <x-form.label for="name">{{ __('Name') }}</x-form.label>
           <x-form.input id="name" name="filter[name]"/>
       </x-form.row>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$forms->total()"/>

    <div class="row my-3">
        @foreach($forms as $form)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $form->name }}</h2>
                    </div>
                    <x-list.group class="list-group-flush">

                    </x-list.group>
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
