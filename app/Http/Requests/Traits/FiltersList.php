<?php

namespace App\Http\Requests\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

trait FiltersList
{
    abstract public function rules(): array;

    public function attributes(): array
    {
        $attributes = [];
        foreach (array_keys($this->rules()) as $attribute) {
            $attributes[$attribute] = $this->mapFilterAttribute($attribute);
        }
        return $attributes;
    }

    public function mapFilterAttribute(string $attribute): string
    {
        if (str_ends_with($attribute, '_from')) {
            return __('validation.attributes.date_from');
        }

        if (str_ends_with($attribute, '_until')) {
            return __('validation.attributes.date_until');
        }

        return $this->getTranslatedAttribute($attribute);
    }

    private function getTranslatedAttribute(string $attribute): string
    {
        $validationKey = str_replace(['filter.', '_from', '_until'], '', $attribute);

        return __('validation.attributes.' . $validationKey);
    }

    public function ruleForDate(?string $afterOrEqual = null): array
    {
        $rules = [
            'nullable',
            'date_format:Y-m-d',
        ];

        if ($afterOrEqual) {
            $rules[] = 'after_or_equal:' . $afterOrEqual;
        }

        return $rules;
    }

    public function ruleForAllowedOrExists(Builder $query, array $allowedValues): array
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) use ($allowedValues, $query) {
                if (!in_array($value, $allowedValues) && !$query->where('id', (int) $value)->exists()) {
                    $fail(trans('validation.exists', [
                        'attribute' => $this->getTranslatedAttribute($attribute),
                    ]));
                }
            },
        ];
    }

    public function ruleForForeignId(string $table): array
    {
        return [
            'nullable',
            Rule::exists($table, 'id'),
        ];
    }

    public function ruleForText(int $maxLength = 255): array
    {
        return [
            'nullable',
            'string',
            'max:' . $maxLength,
        ];
    }
}
