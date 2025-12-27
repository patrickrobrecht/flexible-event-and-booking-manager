<?php

namespace Database\Factories;

use App\Enums\FormElementType;
use App\Models\FormField;
use App\Models\FormFieldValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormFieldValue>
 */
class FormFieldValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'value' => $this->faker->text(30),
        ];
    }

    public function forFormField(FormField $formField): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $this->randomValue($formField),
        ]);
    }

    public function randomValue(FormField $formField): mixed
    {
        $allowedValues = $formField->allowed_values ?? [];

        return match ($formField->type) {
            FormElementType::Checkbox => count($allowedValues) === 1
                ? (int) $this->faker->boolean()
                : $this->faker->randomElements($allowedValues, $this->faker->numberBetween(1, count($allowedValues))),
            FormElementType::Date => $this->faker->date('Y-m-d'),
            FormElementType::DateTime => $this->faker->date('Y-m-d\TH:i'),
            FormElementType::Email => $this->faker->email(),
            FormElementType::Hidden => $allowedValues[0],
            FormElementType::Number => $this->faker->numberBetween(0, 100),
            FormElementType::Radio,
            FormElementType::Select => $this->faker->randomElement($allowedValues),
            FormElementType::Text => $this->faker->text(255),
            FormElementType::Textarea => $this->faker->text(1000),
            default => null,
        };
    }
}
