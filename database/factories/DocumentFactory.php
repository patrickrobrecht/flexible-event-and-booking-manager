<?php

namespace Database\Factories;

use App\Enums\ApprovalStatus;
use App\Enums\FileType;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        /** @var FileType $fileType */
        $fileType = $this->faker->randomElement(FileType::cases());
        $baseName = $this->faker->bothify('????####');
        $extension = $this->faker->randomElement($fileType->getExtensions());

        return [
            'title' => $this->faker->words($this->faker->numberBetween(1, 5), true),
            'description' => $this->faker->boolean(75)
                ? $this->faker->paragraph()
                : null,
            'path' => $baseName . '.' . $extension,
            'file_type' => $fileType,
            'approval_status' => $this->faker->randomElement(ApprovalStatus::cases()),
        ];
    }

    public function forReference(Event|EventSeries|Organization $reference): static
    {
        return $this->for($reference, 'reference')
            ->afterCreating(function (Document $document): void {
                $baseName = pathinfo($document->path, PATHINFO_FILENAME);
                $extension = pathinfo($document->path, PATHINFO_EXTENSION);
                $document->path = $document->reference->getDocumentStoragePath() . '/' . $document->id . '-' . $baseName . '.' . $extension;
                $document->save();
            });
    }
}
