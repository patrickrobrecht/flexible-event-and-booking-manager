<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return $this->redirectWithVerified();
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectWithVerified();
    }

    private function redirectWithVerified(): RedirectResponse
    {
        return redirect()->intended(route('dashboard'))
            ->with('success', __('Your e-mail address has been verified.'));
    }
}
