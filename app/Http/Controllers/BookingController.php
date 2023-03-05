<?php

namespace App\Http\Controllers;

use App\Events\BookingCompleted;
use App\Exports\BookingsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    use StreamsExport;

    public function index(
        Event $event,
        BookingOption $bookingOption,
        BookingFilterRequest $request
    ): StreamedResponse|View {
        $bookingOption->load([
            'form.formFieldGroups.formFields',
        ]);

        $bookingsQuery = Booking::filter($bookingOption->bookings())
            ->with([
                'bookedByUser',
            ]);

        if ($request->query('output') === 'export') {
            $this->authorize('exportAny', Booking::class);

            $fileName = $event->slug . '-' . $bookingOption->slug;
            return $this->streamExcelExport(
                new BookingsExportSpreadsheet($event, $bookingOption, $bookingsQuery->get()),
                str_replace(' ', '-', $fileName) . '.xlsx',
            );
        }

        $this->authorize('viewAny', Booking::class);

        return view('bookings.booking_index', [
            'event' => $event,
            'bookingOption' => $bookingOption,
            'bookings' => $bookingsQuery->paginate(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        return view('bookings.booking_show', [
            'booking' => $booking->loadMissing([
                'bookingOption.form.formFieldGroups.formFields',
            ]),
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
            $message = __('Your booking has been saved successfully.')
                . ' ' . __('We will send you a confirmation by e-mail shortly.');
            Session::flash('success', $message);

            event(new BookingCompleted($booking));

            if (Auth::user()?->can('update', $booking)) {
                return redirect(route('bookings.edit', $booking));
            }

            if (Auth::user()?->can('view', $booking)) {
                return redirect(route('bookings.show', $booking));
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
