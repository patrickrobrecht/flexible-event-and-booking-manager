<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventSeriesRequest;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Models\EventSeries;
use App\Models\Organization;
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
                    'organization',
                    'parentEventSeries',
                    'responsibleUsers',
                ])
                ->withCount([
                    'documents',
                    'events',
                    'subEventSeries',
                ])
                ->paginate(18),
            'organizations' => Organization::query()
                ->whereHas('eventSeries')
                ->orderBy('name')
                ->get(),
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
                'events.responsibleUsers',
                'parentEventSeries',
                'responsibleUsers',
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
                'subEventSeries.responsibleUsers',
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
        /** @phpstan-ignore argument.type */
        if ($eventSeries->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $eventSeries->name]));
            return $this->actionAwareRedirect(
                $request,
                route('event-series.show', $eventSeries),
                createRoute: route('event-series.create')
            );
        }

        return back();
    }

    public function edit(EventSeries $eventSeries): View
    {
        $this->authorize('update', $eventSeries);

        return view('event_series.event_series_form', [
            'eventSeries' => $eventSeries->loadMissing([
                'events' => fn (HasMany $events) => $events->withCount([
                    'documents',
                    'groups',
                ]),
            ]),
            ...$this->formValues(),
        ]);
    }

    public function update(EventSeries $eventSeries, EventSeriesRequest $request): RedirectResponse
    {
        $this->authorize('update', $eventSeries);

        /** @phpstan-ignore argument.type */
        if ($eventSeries->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $eventSeries->name]));
        }

        // Slug may have changed, so we need to generate the URL here!
        return $this->actionAwareRedirect(
            $request,
            route('event-series.show', $eventSeries),
            editRoute: route('event-series.edit', $eventSeries)
        );
    }

    public function destroy(EventSeries $eventSeries): RedirectResponse
    {
        $this->authorize('forceDelete', $eventSeries);

        if ($eventSeries->delete()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $eventSeries->name]));
            return redirect(route('event-series.index'));
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function formValues(): array
    {
        return [
            'allEventSeries' => EventSeries::query()
                ->whereNull('parent_event_series_id')
                ->orderBy('name')
                ->get(),
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ];
    }
}
