<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class EventController extends Controller
{
    public function index(EventFilterRequest $request): View
    {
        $this->authorize('viewAny', Event::class);
        ValueHelper::setDefaults(Event::defaultValuesForQuery());

        return view('events.event_index', $this->formValuesForFilter([
            'events' => Event::buildQueryFromRequest()
                ->with([
                    'bookingOptions' => static fn (HasMany $query) => $query->withCount([
                        'bookings',
                    ]),
                    'eventSeries',
                    'location',
                    'organization',
                    'parentEvent',
                    'responsibleUsers',
                ])
                ->withCount([
                    'documents',
                    'groups',
                    'subEvents',
                ])
                ->paginate(12),
        ]));
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        return view('events.event_show', [
            'event' => $event
                ->loadMissing([
                    'bookingOptions' => static fn (HasMany $query) => $query->withCount([
                        'bookings',
                    ]),
                    'documents.reference',
                    'documents.uploadedByUser',
                    'eventSeries',
                    'location',
                    'organization',
                    'parentEvent.subEvents' => static fn (HasMany $query) => $query->withCount([
                        'documents',
                        'groups',
                    ]),
                    'parentEvent.subEvents.eventSeries',
                    'parentEvent.subEvents.location',
                    'parentEvent.subEvents.responsibleUsers',
                    'responsibleUsers',
                    'subEvents' => static fn (HasMany $query) => $query->withCount([
                        'documents',
                        'groups',
                    ]),
                    'subEvents.eventSeries',
                    'subEvents.location',
                    'subEvents.responsibleUsers',
                ])
                ->loadCount([
                    'groups',
                ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('events.event_form', $this->formValues());
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $event = new Event();
        if ($event->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('events.edit', $event));
        }

        return back();
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('events.event_form', $this->formValues([
            'event' => $event,
        ]));
    }

    public function update(Event $event, EventRequest $request): RedirectResponse
    {
        $this->authorize('update', $event);

        if ($event->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        // Slug may have changed, so we need to generate the URL here!
        return redirect(route('events.edit', $event));
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'events' => Event::query()
                ->whereNull('parent_event_id')
                ->orderBy('started_at')
                ->orderBy('finished_at')
                ->orderBy('name')
                ->get(),
        ], $this->formValuesForFilter($values));
    }

    private function formValuesForFilter(array $values = []): array
    {
        return array_replace([
            'eventSeries' => EventSeries::query()
                ->orderBy('name')
                ->get(),
            'locations' => Location::query()
                ->orderBy('name')
                ->get(),
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ], $values);
    }
}
