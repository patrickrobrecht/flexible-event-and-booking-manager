<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;

enum FormElementType: string
{
    use NamedOption;

    case Headline = 'headline';
    case Date = 'date';
    case DateTime = 'datetime';
    case Email = 'email';
    case File = 'file';
    case Checkbox = 'checkbox';
    case Hidden = 'hidden';
    case Number = 'number';
    case Radio = 'radio';
    case Select = 'select';
    case Text = 'text';
    case Textarea = 'textarea';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Headline => __('headline'),
            self::Date => __('date field'),
            self::DateTime => __('datetime field'),
            self::Email => __('email field'),
            self::File => __('file upload field'),
            self::Checkbox => __('checkbox field'),
            self::Hidden => __('hidden field'),
            self::Number => __('number field'),
            self::Radio => __('radio buttons'),
            self::Select => __('select field'),
            self::Text => __('text field'),
            self::Textarea => __('long text field'),
        };
    }

    public function isFormField(): bool
    {
        return !$this->isStatic();
    }

    public function isStatic(): bool
    {
        return $this === self::Headline;
    }

    /**
     * @return static[]
     */
    public static function casesForFields(): array
    {
        return self::casesFiltered(static fn (self $case) => $case->isFormField());
    }
}
