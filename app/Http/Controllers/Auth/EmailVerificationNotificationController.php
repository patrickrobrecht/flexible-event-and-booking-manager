<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @phpstan-ignore-next-line property.nonObject */
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        /** @phpstan-ignore-next-line property.nonObject */
        $request->user()->sendEmailVerificationNotification();

        return back()
            ->with('success', __('A new verification link has been sent to the e-mail address you provided during registration.'));
    }
}
