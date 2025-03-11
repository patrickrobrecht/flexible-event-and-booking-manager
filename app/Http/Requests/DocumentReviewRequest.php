<?php

namespace App\Http\Requests;

use App\Enums\ApprovalStatus;
use App\Models\Document;
use App\Models\DocumentReview;
use Illuminate\Foundation\Http\FormRequest;

/**
 * For create requests:
 * @property-read ?Document $document
 *
 * For update requests:
 * @property-read ?DocumentReview $document_review
 */
class DocumentReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => [
                'required',
                'string',
            ],
            'approval_status' => [
                $this->routeIs('reviews.store') && $this->user()->can('approve', $this->document)
                    ? 'nullable'
                    : 'prohibited',
                ApprovalStatus::rule(),
            ],
        ];
    }
}
