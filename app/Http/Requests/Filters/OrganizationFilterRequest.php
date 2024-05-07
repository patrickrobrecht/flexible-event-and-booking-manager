<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use App\Options\FilterValue;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Organization}s
 */
class OrganizationFilterRequest extends FormRequest
{
    /** {@see OrganizationPolicy} in {@see OrganizationController} */
    use AuthorizationViaController;
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.event_id' => $this->ruleForAllowedOrExistsInDatabase(Event::query(), FilterValue::values()),
            'filter.location_id' => $this->ruleForAllowedOrExistsInDatabase(Location::query(), [FilterValue::All->value]),
            'filter.document_id' => $this->ruleForAllowedOrExistsInDatabase(Document::query(), FilterValue::values()),
        ];
    }
}
