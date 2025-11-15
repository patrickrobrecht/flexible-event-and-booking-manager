<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ApprovalStatus;
use App\Enums\Visibility;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Requests\DocumentReviewRequest;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentReviewPolicy;
use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(Document::class)]
#[CoversClass(DocumentReview::class)]
#[CoversClass(DocumentReviewController::class)]
#[CoversClass(DocumentReviewPolicy::class)]
#[CoversClass(DocumentReviewRequest::class)]
class DocumentReviewControllerTest extends TestCase
{
    #[DataProvider('referenceClasses')]
    public function testUserCanAddDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider, Ability $commentAbility): void
    {
        $document = self::createDocument($referenceProvider);
        $abilities = [
            DocumentPolicy::VIEW_DOCUMENTS_ABILITIES[$document->reference::class],
            Ability::ViewPrivateEvents,
            Ability::ViewPrivateEventSeries,
            Ability::ViewOrganizations,
        ];

        $this->assertUserCanGetWithAbility("documents/{$document->id}", $abilities)
            ->assertDontSee('Kommentar hinzufügen');
        $this->assertUserCanGetWithAbility("documents/{$document->id}", [...$abilities, $commentAbility])
            ->assertSee('Kommentar hinzufügen');
    }

    #[DataProvider('referenceClasses')]
    public function testUserCanStoreDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider, Ability $ability): void
    {
        $document = self::createDocument($referenceProvider);

        $data = DocumentReview::factory()->makeOne()->toArray();
        $this->assertUserCanPostOnlyWithAbility(
            "documents/{$document->id}/reviews",
            $data,
            $ability,
            $document->getRouteForComments()
        );
    }

    #[DataProvider('referenceClassesWithAbilities')]
    public function testUserCanAddApprovingDocumentReviewOnlyWithCorrectAbility(
        Closure $referenceProvider,
        Ability $commentAbility,
        Ability $approveAbility
    ): void {
        $document = self::createDocument($referenceProvider);
        $data = DocumentReview::factory()->withApprovalStatus()->makeOne()->toArray();

        // Cannot approve with CommentOnDocuments ability only.
        $this->actingAsUserWithAbility($commentAbility);
        $this->post("documents/{$document->id}/reviews", $data)
            ->assertSessionHasErrors([
                'approval_status',
            ]);

        // but can approve with additional ChangeApprovalStatusOfDocuments ability.
        $this->assertUserCanPostWithAbility(
            "documents/{$document->id}/reviews",
            $data,
            [$commentAbility, $approveAbility],
            $document->getRouteForComments()
        );
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClassesWithAbilities(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::CommentOnDocumentsOfEvents, Ability::ChangeApprovalStatusOfDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::CommentOnDocumentsOfEvents, Ability::ChangeApprovalStatusOfDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::CommentOnDocumentsOfEventSeries, Ability::ChangeApprovalStatusOfDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::CommentOnDocumentsOfEventSeries, Ability::ChangeApprovalStatusOfDocumentsOfEventSeries],
            [fn () => self::createOrganization(), Ability::CommentOnDocumentsOfOrganizations, Ability::ChangeApprovalStatusOfDocumentsOfOrganizations],
        ];
    }

    #[DataProvider('referenceClasses')]
    public function testUserCanUpdateDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider, Ability $ability): void
    {
        $user = $this->actingAsUserWithAbility($ability);

        $documentReview = self::createDocumentWithReview($referenceProvider, $user);

        $data = DocumentReview::factory()->makeOne()->toArray();
        $this->put("documents/{$documentReview->document->id}/reviews/{$documentReview->id}", $data)
            ->assertRedirect($documentReview->document->getRouteForComments())
            ->assertSessionHasNoErrors();
    }

    #[DataProvider('referenceClasses')]
    public function testUserCannotUpdateDocumentReviewWithoutComment(Closure $referenceProvider, Ability $ability): void
    {
        $user = $this->actingAsUserWithAbility($ability);
        $documentReview = self::createDocumentWithReview($referenceProvider, $user);

        $this->from($documentReview->document->getRouteForComments())
            ->put("documents/{$documentReview->document->id}/reviews/{$documentReview->id}", [])
            ->assertSessionHasErrors([
                'comment',
            ])
            ->assertRedirect($documentReview->document->getRouteForComments());
    }

    /**
     * @return array<int, array{Closure, Ability}>
     */
    public static function referenceClasses(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public), Ability::CommentOnDocumentsOfEvents],
            [fn () => self::createEvent(Visibility::Private), Ability::CommentOnDocumentsOfEvents],
            [fn () => self::createEventSeries(Visibility::Public), Ability::CommentOnDocumentsOfEventSeries],
            [fn () => self::createEventSeries(Visibility::Private), Ability::CommentOnDocumentsOfEventSeries],
            [fn () => self::createOrganization(), Ability::CommentOnDocumentsOfOrganizations],
        ];
    }
}
