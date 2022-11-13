<?php

namespace App\Http\Requests;

use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Models\Organization;
use App\Options\ActiveStatus;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?Organization $organization
 */
class OrganizationRequest extends FormRequest
{
    /** {@see OrganizationPolicy} in {@see OrganizationController} */
    use AuthorizationViaController;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                ActiveStatus::rule(),
            ],
            'register_entry' => [
                'nullable',
                'string',
                'max:255',
            ],
            'representatives' => [
                'nullable',
                'string',
                'max:255',
            ],
            'website_url' => [
                'nullable',
                'string',
                'max:255',
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
        ];
    }
}
