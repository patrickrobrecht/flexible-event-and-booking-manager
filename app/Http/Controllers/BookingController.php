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
use App\Models\FormFieldValue;
use App\Options\FormElementType;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    use StreamsExport;

    public function index(
        Event $event,
        BookingOption $bookingOption,
        BookingFilterRequest $request
    ): StreamedResponse|View {
        ValueHelper::setDefaults(Booking::defaultValuesForQuery());

        $bookingOption->load([
            'formFields',
        ]);

        $bookingsQuery = Booking::buildQueryFromRequest($bookingOption->bookings())
            ->with([
                'bookedByUser',
                'groups' => fn (BelongsToMany $groups) => $groups->where('event_id', '=', $event->id),
            ]);

        if ($request->query('output') === 'export') {
            $this->authorize('exportBookings', $bookingOption);

            $fileName = $event->slug . '-' . $bookingOption->slug;
            return $this->streamExcelExport(
                new BookingsExportSpreadsheet($event, $bookingOption, $bookingsQuery->get()),
                str_replace(' ', '-', $fileName) . '.xlsx',
            );
        }

        $this->authorize('viewBookings', $bookingOption);

        return view('bookings.booking_index', [
            'event' => $event->load([
                'groups',
            ]),
            'bookingOption' => $bookingOption,
            'bookings' => $bookingsQuery->paginate(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        return view('bookings.booking_show', [
            'booking' => $booking->loadMissing([
                'bookingOption.formFields',
            ]),
        ]);
    }

    public function showPdf(Booking $booking): StreamedResponse
    {
        $this->authorize('viewPDF', $booking);

        $fileName = str_replace(' ', '', implode('-', [
            $booking->id,
            $booking->first_name,
            $booking->last_name,
        ])) . '.pdf';
        $directoryPath = $booking->bookingOption->getFilePath();
        $filePath = $directoryPath . '/' . $fileName;

        if (!Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->makeDirectory($directoryPath);
            Pdf::loadView('bookings.booking_show_pdf', [
                'booking' => $booking->loadMissing([
                    'bookingOption.formFields',
                ]),
            ])
                ->addInfo([
                    'Author' => config('app.owner'),
                    'Title' => implode(' ', [
                        $booking->bookingOption->name,
                        $booking->first_name,
                        $booking->last_name,
                    ]),
                ])
               ->save(Storage::disk('local')->path($filePath));
        }

        return Storage::disk('local')->download($filePath);
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
                'bookingOption.formFields',
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

    public function downloadFile(Booking $booking, FormFieldValue $formFieldValue): StreamedResponse
    {
        $this->authorize('view', $booking);

        if (
            $booking->isNot($formFieldValue->booking)
            || $formFieldValue->formField->type !== FormElementType::File
        ) {
            abort(404);
        }

        return Storage::download($formFieldValue->value);
    }

    public function delete(Booking $booking)
    {
        $this->authorize('delete', $booking);

        if ($booking->delete()) {
            Session::flash('success', __('Deleted successfully.'));
        }

        return back();
    }

    public function restore(Booking $booking)
    {
        $this->authorize('restore', $booking);

        if ($booking->restore()) {
            Session::flash('success', __('Restored successfully.'));
        }

        return back();
    }
}
