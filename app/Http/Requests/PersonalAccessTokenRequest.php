<?php

namespace App\Http\Requests;

use App\Enums\Ability;
use App\Http\Requests\Traits\ValidatesAbilities;
use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property-read ?PersonalAccessToken $personal_access_token
 */
class PersonalAccessTokenRequest extends FormRequest
{
    use ValidatesAbilities;

    protected function getSelectableAbilities(): array
    {
        return Ability::apiCases();
    }

    /**
     * @return array<string, array<int, string|Stringable>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('personal_access_tokens', 'name')
                    ->where('tokenable_id', $this->user()->id)
                    ->ignore($this->personal_access_token->id ?? null),
            ],
            'expires_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],
            ...$this->rulesForAbilities(),
        ];
    }
}
