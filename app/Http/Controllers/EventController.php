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

        return view('events.event_index', [
            ...$this->formValuesForFilter(),
            'events' => Event::buildQueryFromRequest()
                /** @phpstan-ignore-next-line argument.type */
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
        ]);
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
        /** @phpstan-ignore argument.type */
        if ($event->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $event->name]));
            return redirect(route('events.edit', $event));
        }

        return back();
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('events.event_form', [
            'event' => $event,
            ...$this->formValues(),
        ]);
    }

    public function update(Event $event, EventRequest $request): RedirectResponse
    {
        $this->authorize('update', $event);

        /** @phpstan-ignore argument.type */
        if ($event->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $event->name]));
        }

        // Slug may have changed, so we need to generate the URL here!
        return redirect(route('events.edit', $event));
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('forceDelete', $event);

        if ($event->deleteWithGroups()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $event->name]));
            return redirect(route('events.index'));
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function formValues(): array
    {
        return [
            ...$this->formValuesForFilter(),
            'events' => Event::query()
                ->whereNull('parent_event_id')
                ->orderBy('started_at')
                ->orderBy('finished_at')
                ->orderBy('name')
                ->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formValuesForFilter(): array
    {
        return [
            'eventSeries' => EventSeries::query()
                ->orderBy('name')
                ->get(),
            'locations' => Location::query()
                ->orderBy('name')
                ->get(),
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ];
    }
}
