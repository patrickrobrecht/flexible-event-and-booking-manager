<footer class="bg-light d-print-none">
    <div class="container text-center p-3">
        © {{ date('Y') }} {{ config('app.owner') }}

        @php
            $legalNotice = config('app.urls.legal_notice');
            $privacyStatement = config('app.urls.privacy_statement');
            $termsAndConditions = config('app.urls.terms_and_conditions');
        @endphp
        @if($legalNotice || $privacyStatement || $termsAndConditions)
            <x-bs::nav class="justify-content-center">
                @if($legalNotice)
                    <x-bs::nav.item href="{{ $legalNotice }}">{{ __('Legal notice') }}</x-bs::nav.item>
                @endif
                @if($privacyStatement)
                    <x-bs::nav.item href="{{ $privacyStatement }}">{{ __('Privacy') }}</x-bs::nav.item>
                @endif
                @if($termsAndConditions)
                    <x-bs::nav.item href="{{ $termsAndConditions }}">{{ __('General terms and conditions') }}</x-bs::nav.item>
                @endif
            </x-bs::nav>
        @endif
    </div>
</footer>
