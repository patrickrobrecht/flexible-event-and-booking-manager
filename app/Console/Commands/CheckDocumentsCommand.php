<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class CheckDocumentsCommand extends Command
{
    protected $signature = 'app:check-documents';
    protected $description = 'Check whether all documents are saved under the specified path';

    /** @var string[] */
    private array $missingDocumentPaths = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Document::query()
            ->orderBy('id')
            ->chunk(100, fn (Collection $documents) => $this->checkDocuments($documents));

        if (count($this->missingDocumentPaths) === 0) {
            $this->info('All documents ok.');
            return self::SUCCESS;
        }

        $this->error(sprintf('%d documents are missing.', count($this->missingDocumentPaths)));
        return self::FAILURE;
    }

    /**
     * @param Collection<int, Document> $documents
     */
    public function checkDocuments(Collection $documents): void
    {
        foreach ($documents as $document) {
            if (!Storage::exists($document->path)) {
                $this->missingDocumentPaths[] = $document->path;
                $this->error(sprintf('%s missing', $document->path));
            }
        }
    }
}
