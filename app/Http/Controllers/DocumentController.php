<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Http\Requests\Filters\DocumentFilterRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(DocumentFilterRequest $request): View
    {
        $this->authorize('viewAny', Document::class);

        return view('documents.document_index', [
            'documents' => Document::buildQueryFromRequest()
                /** @see Document::scopeVisibleForUser() */
                ->visibleForUser()
                ->with([
                    'reference',
                    'uploadedByUser',
                ])
                ->paginate(20),
        ]);
    }

    public function storeForEvent(DocumentRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('create', [Document::class, $event]);

        return $this->store($request, $event, route('events.show', $event));
    }

    public function storeForEventSeries(DocumentRequest $request, EventSeries $eventSeries): RedirectResponse
    {
        $this->authorize('create', [Document::class, $eventSeries]);

        return $this->store($request, $eventSeries, route('event-series.show', $eventSeries));
    }

    public function storeForLocation(DocumentRequest $request, Location $location): RedirectResponse
    {
        $this->authorize('create', [Document::class, $location]);

        return $this->store($request, $location, route('locations.show', $location));
    }

    public function storeForOrganization(DocumentRequest $request, Organization $organization): RedirectResponse
    {
        $this->authorize('create', [Document::class, $organization]);

        return $this->store($request, $organization, route('organizations.show', $organization));
    }

    private function store(DocumentRequest $request, Event|EventSeries|Location|Organization $reference, string $routeForReference): RedirectResponse
    {
        $document = new Document();
        $document->reference()->associate($reference);
        if ($document->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect($routeForReference);
        }

        return back();
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        return view('documents.document_show', [
            'document' => $document->load([
                'documentReviews.user',
            ]),
        ]);
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return Storage::download($document->path, $document->file_name_from_title);
    }

    public function stream(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return Storage::response($document->path);
    }

    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        return view('documents.document_form', [
            'document' => $document,
        ]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        if ($document->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        return $this->actionAwareRedirect(
            $request,
            route('documents.show', $document),
            editRoute: route('documents.edit', $document)
        );
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('forceDelete', $document);

        if ($document->deleteWithReviews()) {
            Session::flash('success', __('Deleted successfully.'));
        }

        return redirect($document->reference->getRoute());
    }

    public function galleryForEvent(Event $event): View
    {
        return $this->gallery($event);
    }

    public function galleryForEventSeries(EventSeries $eventSeries): View
    {
        return $this->gallery($eventSeries);
    }

    public function galleryForLocation(Location $location): View
    {
        return $this->gallery($location);
    }

    public function galleryForOrganization(Organization $organization): View
    {
        return $this->gallery($organization);
    }

    private function gallery(Event|EventSeries|Location|Organization $reference): View
    {
        $this->authorize('viewAny', [Document::class, $reference]);

        if (!$reference->hasImages()) {
            abort(404);
        }

        return view('documents.document_gallery', [
            'reference' => $reference,
        ]);
    }
}
