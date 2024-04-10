<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\DocumentController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Options\ApprovalStatus;
use App\Options\FileType;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Document}s
 */
class DocumentFilterRequest extends FormRequest
{
    /** {@see DocumentPolicy} in {@see DocumentController::index()} */
    use AuthorizationViaController;
    use FiltersList;

    public function rules(): array
    {
        return [
            'filter.search' => $this->ruleForText(),
            'filter.file_type' => [
                'nullable',
                FileType::rule(),
            ],
            'filter.approval_status' => [
                'nullable',
                ApprovalStatus::rule(),
            ],
            'sort' => [
                'nullable',
                Document::sortOptions()->getRule(),
            ],
        ];
    }
}
