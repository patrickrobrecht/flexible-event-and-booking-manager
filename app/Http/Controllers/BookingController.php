<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\FormElementType;
use App\Events\BookingCompleted;
use App\Exports\BookingsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\BookingPaymentRequest;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\FormField;
use App\Models\FormFieldValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    use StreamsExport;

    public function index(Event $event, BookingOption $bookingOption, BookingFilterRequest $request): Builder|StreamedResponse|View
    {
        ValueHelper::setDefaults(Booking::defaultValuesForQuery());

        /** @var \Illuminate\Database\Eloquent\Builder<Booking> $bookingsQuery */
        $bookingsQuery = Booking::buildQueryFromRequest($bookingOption->bookingsIncludingWaitingList())
            /** @phpstan-ignore-next-line argument.type */
            ->with([
                'bookedByUser',
                'groups' => fn (BelongsToMany $groups) => $groups->where('event_id', '=', $event->id),
            ]);

        $output = $request->query('output');
        if ($output === 'export') {
            $this->authorize('exportBookings', $bookingOption);

            return $this->streamExcelExport(
                new BookingsExportSpreadsheet($event, $bookingOption, $bookingsQuery->get()),
                $event->slug . '-' . $bookingOption->slug . '.xlsx',
            );
        }

        $this->authorize('viewBookings', $bookingOption);

        if ($output === 'pdf') {
            /** @var Collection<int, Booking> $bookings */
            $bookings = $bookingsQuery->get();
            $files = [];

            foreach ($bookings as $booking) {
                $files[Storage::disk('local')->path($booking->storePdfFile())] = $booking->file_name_for_pdf_download;
            }

            return Zip::create($event->slug . '-' . $bookingOption->slug . '.zip', $files);
        }

        if (is_numeric($output)) {
            /** @var FormField $formField */
            $formField = FormField::query()->find((int) $output);

            /** @var Collection<int, FormFieldValue> $formFieldValues */
            $formFieldValues = $formField->formFieldValues()
                ->whereIn('booking_id', $bookingsQuery->toBase()->select('id'))
                ->with([
                    'booking',
                ])
                ->get();

            $files = [];
            foreach ($formFieldValues as $formFieldValue) {
                $formFieldValue->setRelation('formField', $formField);
                $files[Storage::disk('local')->path($formFieldValue->value)] = $formFieldValue->file_name_for_download;
            }

            return Zip::create($event->slug . '-' . $bookingOption->slug . '-' . Str::slug($formField->name) . '.zip', $files);
        }

        return view('bookings.booking_index', [
            'event' => $event->load([
                'groups',
            ]),
            'bookingOption' => $bookingOption,
            'bookings' => $bookingsQuery->paginate(24),
        ]);
    }

    public function indexPayments(Event $event, BookingOption $bookingOption): View
    {
        $this->authorize('viewAnyPaymentStatus', Booking::class);

        return view('bookings.booking_index_payments', [
            'event' => $event,
            'bookingOption' => $bookingOption->load([
                'bookings.groups',
            ]),
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

        return Storage::disk('local')
            ->download($booking->storePdfFile(), $booking->file_name_for_pdf_download);
    }

    public function downloadFile(Booking $booking, FormFieldValue $formFieldValue): StreamedResponse
    {
        $this->authorize('view', $booking);

        if (
            $formFieldValue->formField->type !== FormElementType::File
            || $booking->isNot($formFieldValue->booking)
        ) {
            abort(404);
        }

        return Storage::download($formFieldValue->value, $formFieldValue->file_name_for_download);
    }

    public function store(Event $event, BookingOption $bookingOption, BookingRequest $request): RedirectResponse
    {
        $this->authorize('book', $bookingOption);

        $booking = new Booking();
        $booking->bookingOption()->associate($bookingOption);
        $booking->price = $bookingOption->price;
        $booking->bookedByUser()->associate(Auth::user());
        $booking->booked_at = Carbon::now();
        // If the request contains a booking for the waiting list, validation ensures that the flag is set.
        $booking->status = $request->boolean('confirm_waiting_list') ? BookingStatus::Waiting : BookingStatus::Confirmed;

        if ($booking->fillAndSave($request->validated())) {
            $message = __('Your booking has been saved successfully.')
                . ' ' . __('We will send you a confirmation by e-mail shortly.')
                . ' ' . ($bookingOption->confirmation_text ?? '');
            Session::flash('success', $message);

            event(new BookingCompleted($booking));

            if (Auth::user()?->can('update', $booking) ?? false) {
                return redirect(route('bookings.edit', $booking));
            }

            if (Auth::user()?->can('view', $booking) ?? false) {
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

    public function updatePayments(Event $event, BookingOption $bookingOption, BookingPaymentRequest $request): RedirectResponse
    {
        $this->authorize('updateAnyPaymentStatus', [Booking::class, $bookingOption]);

        $saved = Booking::query()
            ->whereIn('id', $request->validated('booking_id'))
            ->update([
                'paid_at' => $request->validated('paid_at'),
            ]);
        if ($saved > 0) {
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }

    public function delete(Booking $booking): RedirectResponse
    {
        $this->authorize('delete', $booking);

        if ($booking->delete() === true) {
            Session::flash('success', __('Deleted successfully.'));
        }

        return back();
    }

    public function restore(Booking $booking): RedirectResponse
    {
        $this->authorize('restore', $booking);

        if ($booking->restore()) {
            Session::flash('success', __('Restored successfully.'));
        }

        return back();
    }
}
