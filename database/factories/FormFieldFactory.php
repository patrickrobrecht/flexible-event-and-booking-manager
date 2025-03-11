<?php

namespace Database\Factories;

use App\Enums\FormElementType;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormField>
 */
class FormFieldFactory extends Factory
{
    public function definition(): array
    {
        return $this->definitionForType($this->faker->randomElement(FormElementType::casesForFields()));
    }

    private function definitionForType(FormElementType $type): array
    {
        $allowedValues = match ($type) {
            FormElementType::Checkbox => $this->faker->boolean()
                ? [$this->faker->text(35)]
                : $this->arrayOfText($this->faker->numberBetween(2, 10)),
            FormElementType::Hidden => [$this->faker->text(35)],
            FormElementType::Radio,
            FormElementType::Select => $this->arrayOfText($this->faker->numberBetween(2, 10)),
            default => null,
        };

        return [
            'sort' => $this->faker->unique()->numberBetween(),
            'name' => $this->faker->text(35),
            'hint' => $this->faker->boolean(30)
                ? $this->faker->text(30)
                : null,
            'container_class' => 'col-12 col-sm-6 col-md-4 col-lg-3',
            'column' => null,
            'type' => $type,
            'required' => $this->faker->boolean(30),
            'validation_rules' => match ($type) {
                FormElementType::Checkbox => ['accepted'],
                FormElementType::Number => ['gte:0', 'lte:100'],
                FormElementType::Text => ['max:255'],
                default => null,
            },
            'allowed_values' => $allowedValues,
            'editable_after_submission' => $this->faker->boolean(30),
        ];
    }

    private function arrayOfText(int $size): array
    {
        return array_map(fn () => $this->faker->text(20), range(1, $size));
    }

    public function forColumn(string $column): static
    {
        return $this->state(fn (array $attributes) => [
            ...$this->definitionForType('column' === 'email' ? FormElementType::Email : FormElementType::Text),
            'column' => $column,
            'required' => in_array($column, ['first_name', 'last_name', 'email']) ? true : $attributes['required'],
        ]);
    }

    public function forType(FormElementType $type): static
    {
        return $this->state(fn (array $attributes) => $this->definitionForType($type));
    }
}
