<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingOptionRequest;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BookingOptionController extends Controller
{
    public function show(Event $event, BookingOption $bookingOption): View
    {
        $this->authorize('view', $bookingOption);

        return view('booking_options.booking_option_show', [
            'event' => $event,
            'bookingOption' => $bookingOption,
        ]);
    }

    public function create(Event $event): View
    {
        $this->authorize('create', BookingOption::class);

        return view('booking_options.booking_option_form', $this->formValues([
            'event' => $event,
        ]));
    }

    public function store(Event $event, BookingOptionRequest $request): RedirectResponse
    {
        $this->authorize('create', BookingOption::class);

        $bookingOption = new BookingOption();
        $bookingOption->event()->associate($event);
        if ($bookingOption->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('booking-options.edit', [$event, $bookingOption]));
        }

        return back();
    }

    public function edit(Event $event, BookingOption $bookingOption): View
    {
        $this->authorize('update', $bookingOption);

        return view('booking_options.booking_option_form', $this->formValues([
            'bookingOption' => $bookingOption,
            'event' => $event,
        ]));
    }

    public function update(Event $event, BookingOption $bookingOption, BookingOptionRequest $request): RedirectResponse
    {
        $this->authorize('update', $bookingOption);

        if ($bookingOption->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            // Slug may have changed, so we need to generate the URL here!
            return redirect(route('booking-options.edit', [$event, $bookingOption]));
        }

        return back();
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'forms' => Form::query()
                            ->orderBy('name')
                            ->get(),
        ], $values);
    }
}
