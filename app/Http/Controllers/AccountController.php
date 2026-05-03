<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(): View
    {
        $this->authorize('viewAccount', User::class);

        return view('account.account_show');
    }

    public function showAbilities(): View
    {
        $this->authorize('viewAbilities', User::class);

        return view('account.account_show_abilities');
    }

    public function showBookings(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $bookings = $user->bookings()
            ->with([
                'bookingOption.event.location',
            ])
            ->paginate(12);
        $bookings->each(fn (Booking $booking) => $booking->setRelation('bookedByUser', $user));

        return view('account.account_show_bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function edit(): View
    {
        $this->authorize('editAccount', User::class);

        return view('account.account_form');
    }

    public function update(UserRequest $request): RedirectResponse
    {
        $this->authorize('editAccount', User::class);

        /** @var User $user */
        $user = Auth::user();

        if ($user->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            return redirect(route('account.edit'));
        }

        return back();
    }
}
