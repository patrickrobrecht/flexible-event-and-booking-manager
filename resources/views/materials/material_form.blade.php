@extends('layouts.app')

@php
    use App\Models\Material;
    use App\Models\Organization;
    use Illuminate\Database\Eloquent\Collection;
    use Portavice\Bladestrap\Support\Options;

    /** @var ?Material $material */
    /** @var Collection<Organization> $organizations */
@endphp

@section('title')
    @isset($material)
        {{ __('Edit :name', ['name' => $material->name]) }}
    @else
        {{ __('Create material') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('materials.index') }}">{{ __('Materials') }}</x-bs::breadcrumb.item>
    @isset($material)
        <x-bs::breadcrumb.item href="{{ route('materials.show', $material) }}">{{ $material->name }}</x-bs::breadcrumb.item>
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($material) ? 'PUT' : 'POST' }}"
                action="{{ isset($material) ? route('materials.update', $material) : route('materials.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text" :required="true"
                                  :value="$material->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="description" type="textarea"
                                  :value="$material->description ?? null">{{ __('Description') }}</x-bs::form.field>
                <x-bs::form.field name="organization_id" type="radio" :options="Options::fromModels($organizations, 'name')" :required="true"
                                  :value="$material->organization_id ?? null"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
            </div>
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($material){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('materials.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$material ?? null"/>
@endsection
