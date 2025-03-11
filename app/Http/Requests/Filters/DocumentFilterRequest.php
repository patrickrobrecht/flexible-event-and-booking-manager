<?php

namespace App\Http\Requests\Filters;

use App\Enums\ApprovalStatus;
use App\Enums\FileType;
use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Document}s
 */
class DocumentFilterRequest extends FormRequest
{
    use FiltersList;

    public function rules(): array
    {
        return [
            'filter.search' => $this->ruleForText(),
            'filter.file_type' => $this->ruleForAllowedOrExistsInEnum(FileType::class, [FilterValue::All->value]),
            'filter.approval_status' => $this->ruleForAllowedOrExistsInEnum(ApprovalStatus::class, [FilterValue::All->value]),
            'sort' => [
                'nullable',
                Document::sortOptions()->getRule(),
            ],
        ];
    }
}
