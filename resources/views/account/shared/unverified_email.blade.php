@auth
    @php
        $loggedInUser = \Illuminate\Support\Facades\Auth::user();
    @endphp
    @if($loggedInUser->email_verified_at === null)
        <x-bs::alert variant="danger">
            {{ __('Some features are only available with a verified e-mail address.') }}
            {{ __('Your e-mail address :email has not been verified yet.', [
                'email' => $loggedInUser->email,
            ]) }}
            <x-bs::form method="POST" action="{{ route('verification.send') }}">
                <button class="btn btn-link stretched-link ps-0 fw-bold">{{ __('Send verification link via e-mail') }}</button>
            </x-bs::form>
        </x-bs::alert>
    @endif
@endauth
