<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\DocumentController;
use App\Http\Requests\DocumentRequest;
use App\Http\Requests\Filters\DocumentFilterRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use App\Options\Ability;
use App\Options\ApprovalStatus;
use App\Options\FileType;
use App\Options\Visibility;
use App\Policies\DocumentPolicy;
use Closure;
use Database\Factories\DocumentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(Document::class)]
#[CoversClass(DocumentController::class)]
#[CoversClass(DocumentFactory::class)]
#[CoversClass(DocumentFilterRequest::class)]
#[CoversClass(DocumentPolicy::class)]
#[CoversClass(DocumentRequest::class)]
#[CoversClass(FileType::class)]
class DocumentControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testUserCanViewAllDocumentsWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/documents', Ability::ViewDocuments);
    }

    #[DataProvider('referenceClassesWithViewAbility')]
    public function testUserCanViewSingleDocumentWithCorrectAbility(Closure $referenceProvider, Ability $viewReferenceAbility, Ability $viewDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $this->assertUserCanGetOnlyWithAbility("/documents/{$document->id}", [$viewReferenceAbility, Ability::ViewDocuments]);
        $this->assertUserCanGetOnlyWithAbility("/documents/{$document->id}", [$viewReferenceAbility, $viewDocumentsAbility]);
    }

    public static function referenceClassesWithViewAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::ViewEvents, Ability::ViewDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::ViewPrivateEvents, Ability::ViewDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::ViewEventSeries, Ability::ViewDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::ViewPrivateEventSeries, Ability::ViewDocumentsOfEventSeries],
            [fn () => self::createOrganization(), Ability::ViewOrganizations, Ability::ViewDocumentsOfOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithViewAndCreateAbility')]
    public function testUserCanAddDocumentOnlyWithCorrectAbility(Closure $referenceProvider, Ability $viewReferenceAbility, Ability $addDocumentsAbility): void
    {
        $reference = $referenceProvider();
        $storeUri = match ($reference::class) {
            Event::class => "events/{$reference->slug}/documents",
            EventSeries::class => "event-series/{$reference->slug}/documents",
            Organization::class => "organizations/{$reference->id}/documents",
        };

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $data = [
            'title' => 'Sample Document',
            'description' => 'This is a sample description.',
            'file' => $file,
        ];

        $this->assertUserCanPostOnlyWithAbility($storeUri, $data, [$viewReferenceAbility, $addDocumentsAbility], $reference->getRoute());
    }

    public static function referenceClassesWithViewAndCreateAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::ViewEvents, Ability::AddDocumentsToEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::ViewPrivateEvents, Ability::AddDocumentsToEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::ViewEventSeries, Ability::AddDocumentsToEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::ViewPrivateEventSeries, Ability::AddDocumentsToEventSeries],
            [fn () => self::createOrganization(), Ability::ViewOrganizations, Ability::AddDocumentsToOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithViewAndEditAbility')]
    public function testUserCanOpenEditDocumentFormOnlyWithCorrectAbility(Closure $referenceProvider, Ability $viewReferenceAbility, Ability $editDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $this->assertUserCanGetOnlyWithAbility("/documents/{$document->id}/edit", [$viewReferenceAbility, $editDocumentsAbility]);
    }

    #[DataProvider('referenceClassesWithViewAndEditAbility')]
    public function testUserCanUpdateDocumentOnlyWithCorrectAbility(Closure $referenceProvider, Ability $viewReferenceAbility, Ability $editDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $data = [
            'title' => 'NEW ' . $document->title,
        ];
        $editRoute = route('documents.edit', $document);
        $this->assertUserCanPutOnlyWithAbility(
            "/documents/{$document->id}",
            $data,
            [$viewReferenceAbility, $editDocumentsAbility],
            $editRoute,
            $editRoute
        );
    }

    public static function referenceClassesWithViewAndEditAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::ViewEvents, Ability::EditDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::ViewPrivateEvents, Ability::EditDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::ViewEventSeries, Ability::EditDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::ViewPrivateEventSeries, Ability::EditDocumentsOfEventSeries],
            [fn () => self::createOrganization(), Ability::ViewOrganizations, Ability::EditDocumentsOfOrganizations],
        ];
    }

    #[DataProvider('referenceClassesWithViewAndDeleteAbility')]
    public function testDeletionIsPossibleOnlyWithCorrectAbility(Closure $referenceProvider, Ability $viewReferenceAbility, Ability $deleteDocumentsAbility): void
    {
        $document = self::createDocument($referenceProvider);

        $this->assertUserCanDeleteOnlyWithAbility("/documents/{$document->id}", [$viewReferenceAbility, $deleteDocumentsAbility], $document->reference->getRoute());
    }

    public static function referenceClassesWithViewAndDeleteAbility(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::ViewEvents, Ability::DeleteDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::ViewPrivateEvents, Ability::DeleteDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::ViewEventSeries, Ability::DeleteDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::ViewPrivateEventSeries, Ability::DeleteDocumentsOfEventSeries],
            [fn () => self::createOrganization(), Ability::ViewOrganizations, Ability::DeleteDocumentsOfOrganizations],
        ];
    }
}
