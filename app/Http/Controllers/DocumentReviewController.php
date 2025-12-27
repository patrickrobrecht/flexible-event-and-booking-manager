<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Http\Requests\DocumentReviewRequest;
use App\Models\Document;
use App\Models\DocumentReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocumentReviewController extends Controller
{
    public function store(DocumentReviewRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('create', [DocumentReview::class, $document]);

        $validated = $request->validated();

        // Change approval status of the document if requested.
        /** @var ?string $approvalStatus */
        $approvalStatus = $request->validated('approval_status');
        if ($approvalStatus !== null) {
            $approvalStatus = ApprovalStatus::tryFrom($approvalStatus);
            if ($approvalStatus !== null) {
                if ($document->approval_status === $approvalStatus) {
                    // Don't save approval status in review.
                    $validated['approval_status'] = null;
                } else {
                    // Change the approval status of the document.
                    $document->approval_status = $approvalStatus;
                }
            }
        }

        $review = new DocumentReview();
        $review->document()->associate($request->document);
        $review->user()->associate(Auth::user());
        if ($review->fillAndSave($validated) && $document->save()) {
            Session::flash('success', __('Saved comment successfully.'));
        }

        return redirect($document->getRouteForComments());
    }

    public function update(DocumentReviewRequest $request, Document $document, DocumentReview $review): RedirectResponse
    {
        $this->authorize('update', $review);

        if ($review->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved comment successfully.'));
        }

        return redirect($document->getRouteForComments());
    }
}
