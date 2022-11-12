<footer class="bg-light">
    <div class="container text-center p-3">
        © {{ date('Y') }} {{ config('app.owner') }}

        @php
            $legalNotice = config('app.urls.legal_notice');
            $privacyStatement = config('app.urls.privacy_statement');
            $termsAndConditions = config('app.urls.terms_and_conditions');
        @endphp
        @if($legalNotice || $privacyStatement || $termsAndConditions)
            <ul class="nav justify-content-center">
                @if($legalNotice)
                    <x-nav.item href="{{ $legalNotice }}">{{ __('Legal notice') }}</x-nav.item>
                @endif
                @if($privacyStatement)
                    <x-nav.item href="{{ $privacyStatement }}">{{ __('Privacy') }}</x-nav.item>
                @endif
                @if($termsAndConditions)
                    <x-nav.item href="{{ $termsAndConditions }}">{{ __('General terms and conditions') }}</x-nav.item>
                @endif
            </ul>
        @endif
    </div>
</footer>
