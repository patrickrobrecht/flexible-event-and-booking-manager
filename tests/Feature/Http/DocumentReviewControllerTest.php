<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\DocumentReviewController;
use App\Http\Requests\DocumentReviewRequest;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Options\Ability;
use App\Options\ApprovalStatus;
use App\Options\Visibility;
use App\Policies\DocumentReviewPolicy;
use Closure;
use Database\Factories\DocumentReviewFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(Document::class)]
#[CoversClass(DocumentReview::class)]
#[CoversClass(DocumentReviewController::class)]
#[CoversClass(DocumentReviewFactory::class)]
#[CoversClass(DocumentReviewPolicy::class)]
#[CoversClass(DocumentReviewRequest::class)]
class DocumentReviewControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    #[DataProvider('referenceClasses')]
    public function testUserCanAddDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider): void
    {
        $document = self::createDocument($referenceProvider);

        $data = DocumentReview::factory()->makeOne()->toArray();
        $this->assertUserCanPostOnlyWithAbility(
            "documents/{$document->id}/reviews",
            $data,
            Ability::CommentOnDocuments,
            $document->getRouteForComments()
        );
    }

    #[DataProvider('referenceClasses')]
    public function testUserCanAddApprovingDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider): void
    {
        $document = self::createDocument($referenceProvider);
        $data = DocumentReview::factory()->withApprovalStatus()->makeOne()->toArray();

        // Cannot approve with CommentOnDocuments ability only.
        $this->actingAsUserWithAbility(Ability::CommentOnDocuments);
        $this->post("documents/{$document->id}/reviews", $data)
            ->assertSessionHasErrors([
                'approval_status',
            ]);

        // but can approve with additional ChangeApprovalStatusOfDocuments ability.
        $this->assertUserCanPostWithAbility(
            "documents/{$document->id}/reviews",
            $data,
            [Ability::CommentOnDocuments, Ability::ChangeApprovalStatusOfDocuments],
            $document->getRouteForComments()
        );
    }

    #[DataProvider('referenceClasses')]
    public function testUserCanUpdateDocumentReviewOnlyWithCorrectAbility(Closure $referenceProvider): void
    {
        $user = $this->actingAsUserWithAbility(Ability::CommentOnDocuments);

        $documentReview = self::createDocumentWithReview($referenceProvider, $user);

        $data = DocumentReview::factory()->makeOne()->toArray();
        $this->put("documents/{$documentReview->document->id}/reviews/{$documentReview->id}", $data)
            ->assertRedirect($documentReview->document->getRouteForComments())
            ->assertSessionHasNoErrors();
    }

    #[DataProvider('referenceClasses')]
    public function testUserCannotUpdateDocumentReviewWithoutComment(Closure $referenceProvider): void
    {
        $user = $this->actingAsUserWithAbility(Ability::CommentOnDocuments);
        $documentReview = self::createDocumentWithReview($referenceProvider, $user);

        $this->from($documentReview->document->getRouteForComments())
            ->put("documents/{$documentReview->document->id}/reviews/{$documentReview->id}", [])
            ->assertSessionHasErrors([
                'comment',
            ])
            ->assertRedirect($documentReview->document->getRouteForComments());
    }

    public static function referenceClasses(): array
    {
        return [
            [fn () => self::createEvent(Visibility::Public)],
            [fn () => self::createEvent(Visibility::Private)],
            [fn () => self::createEventSeries(Visibility::Public)],
            [fn () => self::createEventSeries(Visibility::Private)],
            [fn () => self::createOrganization()],
        ];
    }
}
