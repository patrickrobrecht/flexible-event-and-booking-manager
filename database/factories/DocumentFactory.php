<?php

namespace Database\Factories;

use App\Enums\ApprovalStatus;
use App\Enums\FileType;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->words($this->faker->numberBetween(1, 5), true),
            'description' => $this->faker->boolean(75)
                ? $this->faker->paragraph()
                : null,
            'path' => $this->faker->filePath(),
            'file_type' => $this->faker->randomElement(FileType::cases()),
            'approval_status' => $this->faker->randomElement(ApprovalStatus::cases()),
        ];
    }
}
