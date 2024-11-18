<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesResponsibleUsers;
use App\Models\Organization;
use App\Options\ActiveStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?Organization $organization
 */
class OrganizationRequest extends FormRequest
{
    use ValidatesResponsibleUsers;

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
                'url:http,https',
                'max:255',
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
            ...$this->rulesForResponsibleUsers(),
        ];
    }

    public function attributes(): array
    {
        return $this->attributesForResponsibleUsers();
    }
}
