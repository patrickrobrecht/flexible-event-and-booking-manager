@component('mail::layout')

{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.owner') }}

@php
    $legalNotice = config('app.urls.legal_notice');
    $privacyStatement = config('app.urls.privacy_statement');
    $termsAndConditions = config('app.urls.terms_and_conditions');
@endphp
@if($legalNotice || $privacyStatement || $termsAndConditions)
@if($legalNotice)
<a href="{{ $legalNotice }}">{{ __('Legal notice') }}</a>
@endif
@if($privacyStatement)
<a href="{{ $privacyStatement }}">{{ __('Privacy') }}</a>
@endif
@if($termsAndConditions)
<a href="{{ $termsAndConditions }}">{{ __('General terms and conditions') }}</a>
@endif
@endif{{-- end links --}}
@endcomponent
@endslot
@endcomponent
