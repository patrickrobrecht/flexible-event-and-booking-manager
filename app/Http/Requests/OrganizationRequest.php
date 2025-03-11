<?php

namespace App\Http\Requests;

use App\Enums\ActiveStatus;
use App\Http\Requests\Traits\ValidatesResponsibleUsers;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Validation\Rules\Iban;

/**
 * @property ?Organization $organization
 */
class OrganizationRequest extends FormRequest
{
    use ValidatesResponsibleUsers;

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Replace whitespace etc. with "-"
            'slug' => isset($this->slug) ? Str::slug($this->slug) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $hasEventsWithPaidBookingOptions = isset($this->organization)
            && $this->organization->events()
                ->whereHas('bookingOptions', fn (Builder $bookingOptions) => $bookingOptions->whereNotNull('price'))
                ->exists();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('organizations', 'slug')
                    ->ignore($this->organization ?? null),
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
            'phone' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^([0-9\s\ \+\(\)]*)$/',
            ],
            'email' => [
                'nullable',
                'email',
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
            'bank_account_holder' => [
                'nullable', // name is fallback
                'string',
                'max:255',
            ],
            'iban' => [
                $hasEventsWithPaidBookingOptions ? 'required' : 'nullable',
                'required_with:bank_account_holder',
                'required_with:bank_name',
                new Iban(),
            ],
            'bank_name' => [
                $hasEventsWithPaidBookingOptions ? 'required' : 'nullable',
                'required_with:bank_account_holder',
                'required_with:iban',
                'string',
                'max:255',
            ],
            ...$this->rulesForResponsibleUsers(),
        ];
    }

    public function attributes(): array
    {
        return $this->attributesForResponsibleUsers();
    }
}
