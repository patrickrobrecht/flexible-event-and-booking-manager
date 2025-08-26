<?php

namespace App\Http\Requests\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Stringable;

trait FiltersList
{
    abstract public function rules(): array;

    /**
     * @return array<string, string>
     */
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

    /**
     * @return string[]
     */
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

    /**
     * @template TModel of Model
     * @param Builder<TModel> $query
     * @param array<int, int|string> $allowedValues
     * @return array<int, string|Closure>
     */
    public function ruleForAllowedOrExistsInDatabase(Builder $query, array $allowedValues): array
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) use ($allowedValues, $query) {
                if (!in_array($value, $allowedValues, true) && !$query->where('id', (int) $value)->exists()) {
                    $fail(trans('validation.exists', [
                        'attribute' => $this->getTranslatedAttribute($attribute),
                    ]));
                }
            },
        ];
    }

    /**
     * @param class-string $enumClass
     * @param array<int, int|string> $allowedValues
     * @return array<int, string|Closure>
     */
    public function ruleForAllowedOrExistsInEnum(string $enumClass, array $allowedValues): array
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) use ($allowedValues, $enumClass) {
                if (!in_array($value, $allowedValues, true) && !$enumClass::exists($value)) {
                    $fail(trans('validation.exists', [
                        'attribute' => $this->getTranslatedAttribute($attribute),
                    ]));
                }
            },
        ];
    }

    /**
     * @return array<int, string|Stringable>
     */
    public function ruleForForeignId(string $table): array
    {
        return [
            'nullable',
            Rule::exists($table, 'id'),
        ];
    }

    /**
     * @return string[]
     */
    public function ruleForText(int $maxLength = 255): array
    {
        return [
            'nullable',
            'string',
            'max:' . $maxLength,
        ];
    }
}
