<?php

namespace Database\Factories;

use App\Models\DocumentReview;
use App\Options\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentReview>
 */
class DocumentReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment' => $this->faker->sentences($this->faker->numberBetween(1, 10), true),
        ];
    }

    public function withApprovalStatus(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => $this->faker->randomElement(ApprovalStatus::cases())->value,
        ]);
    }
}
