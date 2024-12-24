<?php

namespace Database\Factories;

use App\Models\FormField;
use App\Models\FormFieldValue;
use App\Options\FormElementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormFieldValue>
 */
class FormFieldValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
        return match ($formField->type) {
            FormElementType::Checkbox => $this->faker->randomElements($formField->allowed_values, $this->faker->numberBetween(1, count($formField->allowed_values))),
            FormElementType::Date => $this->faker->date('Y-m-d'),
            FormElementType::DateTime => $this->faker->date('Y-m-d\TH:i'),
            FormElementType::Email => $this->faker->email(),
            FormElementType::Hidden => $formField->allowed_values[0],
            FormElementType::Number => $this->faker->numberBetween(0, 100),
            FormElementType::Radio,
            FormElementType::Select => $this->faker->randomElement($formField->allowed_values),
            FormElementType::Text => $this->faker->text(255),
            FormElementType::Textarea => $this->faker->text(1000),
            default => null,
        };
    }
}
