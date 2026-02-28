@extends('layouts.app')

@php
    /** @var \App\Models\Document $document */
@endphp

@section('title')
    {{ $document->title }}
@endsection

@section('breadcrumbs')
    @include('documents.shared.document_breadcrumbs', [
        'reference' => $document->reference,
    ])
    <x-bs::breadcrumb.item>{{ $document->title }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1><i class="{{ $document->file_type->getIconClass() }}" title="{{ $document->file_type->getTranslatedName() }}"></i> @yield('title')</h1>
@endsection

@section('headline-buttons')
    @can('update', $document)
        <x-button.edit href="{{ route('documents.edit', $document) }}"/>
    @endcan
    @include('documents.shared.document_delete_modal_button')
@endsection

@section('content')
    @include('documents.shared.document_delete_modal')

    <x-badge.approval-status :approval-status="$document->approval_status"/>
    <x-bs::badge>
        <i class="fa fa-fw fa-user"></i>
        @include('documents.shared.document_uploaded_by', [
            'class' => 'link-light',
        ])
    </x-bs::badge>

    <div class="row mt-4">
        <div class="col-12 col-lg-9">
            @include('documents.shared.document_embed')

            <x-text.timestamp :model="$document"/>
        </div>
        <div class="col-12 mt-4 mt-lg-0 col-lg-3">
            @can('viewAny', [\App\Models\DocumentReview::class, $document])
                <section id="comments">
                    <h2>{{ __('Comments') }}</h2>
                    @if($document->documentReviews->isEmpty())
                        <x-bs::alert>{{ __('This document has not received any comments yet.') }}</x-bs::alert>
                    @else
                        @foreach($document->documentReviews as $documentReview)
                            <div class="card mb-3 avoid-break">
                                <div class="card-header">
                                    <div>
                                        <strong>{{ __(':name commented at :created_at.', [
                                            'name' => $documentReview->user->name,
                                            'created_at' => formatDateTime($documentReview->created_at),
                                        ]) }}</strong>
                                        @if($documentReview->updated_at->isAfter($documentReview->created_at))
                                            <small class="text-muted">
                                                {{ __('Updated at :updated_at', [
                                                    'updated_at' => formatDateTime($documentReview->updated_at),
                                                ]) }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <i class="fa fa-fw fa-circle-question"></i>
                                        @isset($documentReview->approval_status)
                                            <x-badge.approval-status :approval-status="$documentReview->approval_status"/>
                                        @else
                                            {{ __('Approval status unchanged') }}
                                        @endisset
                                    </div>

                                    <div><i class="fa fa-fw fa-comment"></i> {{ $documentReview->comment }}</div>
                                    @can('update', $documentReview)
                                        @php
                                            $editFormId = 'form-review-' . $documentReview->id;
                                        @endphp
                                        <x-bs::button.link class="mt-3" data-bs-toggle="collapse" href="{{ '#' . $editFormId }}">
                                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                                        </x-bs::button.link>
                                        <x-bs::form id="{{ $editFormId }}" class="collapse"
                                                    method="PUT" action="{{ route('reviews.update', [$document, $documentReview]) }}">
                                            <x-bs::form.field name="comment" type="textarea" rows="5"
                                                              :value="$documentReview->comment ?? null">
                                                <i class="fa fa-fw fa-comment"></i> {{ __('Comment') }}
                                            </x-bs::form.field>
                                            <x-bs::button><i class="fa fa-fw fa-save"></i> {{ __('Save') }}</x-bs::button>
                                        </x-bs::form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @can('create', [\App\Models\DocumentReview::class, $document])
                        <div class="mt-3 d-print-none">
                            <h3>{{ __('Add comment') }}</h3>
                            <x-bs::form method="POST" action="{{ route('reviews.store', $document) }}">
                                <x-bs::form.field name="comment" type="textarea" rows="5">
                                    <i class="fa fa-fw fa-comment"></i> {{ __('Comment') }}
                                </x-bs::form.field>
                                @can('approve', $document)
                                    <x-bs::form.field name="approval_status" type="radio" :options="\App\Enums\ApprovalStatus::toOptions()"
                                                      :value="$document->approval_status->value ?? null"><i class="fa fa-fw fa-circle-question"></i> {{ __('Approval status') }}</x-bs::form.field>
                                @endcan
                                <x-bs::button><i class="fa fa-fw fa-save"></i> {{ __('Add comment') }}</x-bs::button>
                            </x-bs::form>
                        </div>
                    @endcan
                </section>
            @endcan
        </div>
    </div>
@endsection
