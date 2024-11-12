<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\CheckDocumentsCommand;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(CheckDocumentsCommand::class)]
#[CoversClass(Document::class)]
class CheckDocumentsCommandTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testAllDocumentsOk(): void
    {
        self::createDocuments();

        Storage::shouldReceive('exists')
            ->andReturn(true);

        $this->artisan('app:check-documents')
            ->expectsOutput('All documents ok.')
            ->assertSuccessful();
    }

    public function testCommandDetectsMissingDocuments(): void
    {
        $existingDocuments = self::createDocuments();
        foreach ($existingDocuments as $document) {
            Storage::shouldReceive('exists')
                ->with($document->path)
                ->andReturn(true);
        }

        $missingDocuments = self::createDocuments();
        foreach ($missingDocuments as $document) {
            Storage::shouldReceive('exists')
                ->with($document->path)
                ->andReturn(false);
        }

        $command = $this->artisan('app:check-documents')
            ->expectsOutput(sprintf('%d documents are missing.', count($missingDocuments)));
        foreach ($missingDocuments as $document) {
            $command->expectsOutputToContain($document->path . ' missing');
        }
        $command->assertExitCode(Command::FAILURE);
    }
}
