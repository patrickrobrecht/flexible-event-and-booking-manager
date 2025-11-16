<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ApprovalStatus;
use App\Enums\FileType;
use App\Enums\Visibility;
use App\Http\Controllers\DocumentController;
use App\Http\Requests\DocumentRequest;
use App\Http\Requests\Filters\DocumentFilterRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Policies\DocumentPolicy;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(Document::class)]
#[CoversClass(DocumentController::class)]
#[CoversClass(DocumentFilterRequest::class)]
#[CoversClass(DocumentPolicy::class)]
#[CoversClass(DocumentRequest::class)]
#[CoversClass(FileType::class)]
class DocumentControllerTest extends TestCase
{
    public function testUserCanViewAllDocumentsWithCorrectAbility(): void
    {
        foreach (DocumentPolicy::VIEW_DOCUMENTS_ABILITIES as $ability) {
            $this->assertUserCanGetWithAbility('/documents', $ability);
        }
        $this->assertUserCannotGetDespiteAbility('/documents', Ability::casesExcept(DocumentPolicy::VIEW_DOCUMENTS_ABILITIES));
    }

    #[DataProvider('referenceClassesWithViewAbility')]
    public function testUserCanViewSingleDocumentWithCorrectAbility(Closure $referenceProvider, Ability $viewDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        Storage::shouldReceive('download')
            ->with($document->path, $document->file_name_from_title)
            ->andReturn(new StreamedResponse(fn () => 'Sample content'));
        Storage::shouldReceive('response')
            ->with($document->path)
            ->andReturn(new StreamedResponse(fn () => 'Sample content'));

        $urlPrefix = "/documents/{$document->id}";
        foreach ([$urlPrefix, $urlPrefix . '/download', $urlPrefix . '/stream'] as $documentUrl) {
            $this->assertUserCanGetOnlyWithAbility($documentUrl, $viewDocumentsAbility);
        }
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClassesWithViewAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::ViewDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::ViewDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::ViewDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::ViewDocumentsOfEventSeries],
            [fn () => self::createLocation(), Ability::ViewDocumentsOfLocations],
            [fn () => self::createOrganization(), Ability::ViewDocumentsOfOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithCreateAbility')]
    public function testUserCanAddDocumentWithCorrectAbility(Closure $referenceProvider, Ability $addDocumentsAbility): void
    {
        /** @var Event|EventSeries|Location|Organization $reference */
        $reference = $referenceProvider();
        /** @phpstan-ignore match.unhandled */
        $storeUri = match ($reference::class) {
            Event::class => "events/{$reference->slug}/documents",
            EventSeries::class => "event-series/{$reference->slug}/documents",
            Location::class => "locations/{$reference->id}/documents",
            Organization::class => "organizations/{$reference->slug}/documents",
        };

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $data = [
            'title' => 'Sample Document',
            'description' => 'This is a sample description.',
            'file' => $file,
        ];

        $this->assertUserCanPostWithAbility($storeUri, $data, [$addDocumentsAbility], $reference->getRoute());
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClassesWithCreateAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::AddDocumentsToEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::AddDocumentsToEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::AddDocumentsToEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::AddDocumentsToEventSeries],
            [fn () => self::createLocation(), Ability::AddDocumentsToLocations],
            [fn () => self::createOrganization(), Ability::AddDocumentsToOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithEditAbility')]
    public function testUserCanOpenEditDocumentFormOnlyWithCorrectAbility(Closure $referenceProvider, Ability $editDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $this->assertUserCanGetOnlyWithAbility("/documents/{$document->id}/edit", $editDocumentsAbility);
    }

    #[DataProvider('referenceClassesWithEditAbility')]
    public function testUserCanUpdateDocumentWithCorrectAbility(Closure $referenceProvider, Ability $editDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $data = [
            'title' => 'NEW ' . $document->title,
        ];
        $this->assertUserCanPutWithAbility(
            "/documents/{$document->id}",
            $data,
            $editDocumentsAbility,
            route('documents.edit', $document),
            route('documents.show', $document)
        );
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClassesWithEditAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::EditDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::EditDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::EditDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::EditDocumentsOfEventSeries],
            [fn () => self::createLocation(), Ability::EditDocumentsOfLocations],
            [fn () => self::createOrganization(), Ability::EditDocumentsOfOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithDeleteAbility')]
    public function testDeletionIsPossibleOnlyWithCorrectAbility(Closure $referenceProvider, Ability $deleteDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        Storage::shouldReceive('delete')
            ->with($document->path)
            ->once()
            ->andReturn(true);

        $this->assertUserCanDeleteOnlyWithAbility("/documents/{$document->id}", $deleteDocumentsAbility, $document->reference->getRoute());
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClassesWithDeleteAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::DestroyDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::DestroyDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::DestroyDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::DestroyDocumentsOfEventSeries],
            [fn () => self::createLocation(), Ability::DestroyDocumentsOfLocations],
            [fn () => self::createOrganization(), Ability::DestroyDocumentsOfOrganizations],
        ];
    }
}
