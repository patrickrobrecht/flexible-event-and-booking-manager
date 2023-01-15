<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Event $event, BookingOption $bookingOption): View
    {
        $this->authorize('viewAny', Booking::class);

        return view('bookings.booking_index', [
            'event' => $event,
            'bookingOption' => $bookingOption,
            'bookings' => Booking::filter($bookingOption->bookings())->paginate(),
        ]);
    }

    public function store(Event $event, BookingOption $bookingOption, BookingRequest $request): RedirectResponse
    {
        $this->authorize('book', $bookingOption);

        $booking = new Booking();
        $booking->bookingOption()->associate($bookingOption);
        $booking->price = $bookingOption->price;
        $booking->bookedByUser()->associate(Auth::user());
        $booking->booked_at = Carbon::now();

        if ($booking->fillAndSave($request->validated())) {
            Session::flash('success', __('Your booking has been saved successfully.'));

            if (Auth::user()?->can('update', $booking)) {
                return redirect(route('bookings.edit', $booking));
            }
        }

        return back();
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);

        return view('bookings.booking_form', [
            'booking' => $booking->loadMissing([
                'bookingOption.form.formFieldGroups.formFields',
            ]),
        ]);
    }

    public function update(Booking $booking, BookingRequest $request): RedirectResponse
    {
        $this->authorize('update', $booking);

        if ($booking->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            return redirect(route('bookings.edit', $booking));
        }

        return back();
    }
}
