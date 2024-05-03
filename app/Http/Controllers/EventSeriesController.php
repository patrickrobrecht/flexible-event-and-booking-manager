<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventSeriesRequest;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Models\EventSeries;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class EventSeriesController extends Controller
{
    public function index(EventSeriesFilterRequest $request): View
    {
        $this->authorize('viewAny', EventSeries::class);
        ValueHelper::setDefaults(EventSeries::defaultValuesForQuery());

        return view('event_series.event_series_index', [
            'eventSeries' => EventSeries::buildQueryFromRequest()
                ->with([
                    'parentEventSeries',
                ])
                ->withCount([
                    'documents',
                    'events',
                    'subEventSeries',
                ])
                ->paginate(),
        ]);
    }

    public function show(EventSeries $eventSeries): View
    {
        $this->authorize('view', $eventSeries);

        return view('event_series.event_series_show', [
            'eventSeries' => $eventSeries->loadMissing([
                'documents.reference',
                'documents.uploadedByUser',
                'events' => fn (HasMany $events) => $events
                    ->withCount([
                        'documents',
                        'groups',
                    ]),
                'events.bookingOptions' => fn (HasMany $bookingOptions) => $bookingOptions
                    ->withCount([
                        'bookings',
                    ]),
                'events.location',
                'events.parentEvent',
                'parentEventSeries',
                'subEventSeries' => fn (HasMany $subEventSeries) => $subEventSeries
                    ->withCount([
                        'documents',
                        'events',
                    ])
                    ->withMin('events', 'started_at')
                    ->withMax('events', 'started_at')
                    ->withCasts([
                        'events_min_started_at' => 'datetime',
                        'events_max_started_at' => 'datetime',
                    ]),
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', EventSeries::class);

        return view('event_series.event_series_form', $this->formValues());
    }

    public function store(EventSeriesRequest $request): RedirectResponse
    {
        $this->authorize('create', EventSeries::class);

        $eventSeries = new EventSeries();
        if ($eventSeries->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('event-series.edit', $eventSeries));
        }

        return back();
    }

    public function edit(EventSeries $eventSeries): View
    {
        $this->authorize('update', $eventSeries);

        return view('event_series.event_series_form', $this->formValues([
            'eventSeries' => $eventSeries->loadMissing([
                'events' => fn (HasMany $events) => $events->withCount([
                    'documents',
                    'groups',
                ]),
            ]),
        ]));
    }

    public function update(EventSeries $eventSeries, EventSeriesRequest $request): RedirectResponse
    {
        $this->authorize('update', $eventSeries);

        if ($eventSeries->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            // Slug may have changed, so we need to generate the URL here!
            return redirect(route('event-series.edit', $eventSeries));
        }

        return back();
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'allEventSeries' => EventSeries::query()
                ->whereNull('parent_event_series_id')
                ->orderBy('name')
                ->get(),
        ], $values);
    }
}
