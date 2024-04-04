<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function storeForEvent(DocumentRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('create', [Document::class, $event]);

        return $this->store($request, $event, route('events.show', $event));
    }

    private function store(DocumentRequest $request, Model $reference, string $routeForReference): RedirectResponse
    {
        $document = new Document();
        $document->reference()->associate($reference);
        if ($document->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect($routeForReference);
        }

        return back();
    }

    public function download(Document $document): StreamedResponse
    {
        $this->authorize('view', $document);

        return Storage::download($document->path);
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
            return redirect(route('documents.edit', $document));
        }

        return back();
    }

    public function destroy(Document $document)
    {
        $this->authorize('forceDelete', $document);

        if ($document->delete()) {
            Session::flash('success', __('Deleted successfully.'));
            return redirect($document->reference->getRoute());
        }

        return back();
    }
}
