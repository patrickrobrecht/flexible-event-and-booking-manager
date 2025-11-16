<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use App\Policies\Traits\ChecksAbilitiesForDocuments;
use Illuminate\Auth\Access\Response;

class DocumentReviewPolicy
{
    use ChecksAbilities;
    use ChecksAbilitiesForDocuments;

    public const array VIEW_COMMENTS_ON_DOCUMENTS_ABILITIES = [
        Event::class => Ability::ViewCommentsOnDocumentsOfEvents,
        EventSeries::class => Ability::ViewCommentsOnDocumentsOfEventSeries,
        Organization::class => Ability::ViewCommentsOnDocumentsOfOrganizations,
    ];

    public const array COMMENT_ON_DOCUMENTS_ABILITIES = [
        Event::class => Ability::CommentOnDocumentsOfEvents,
        EventSeries::class => Ability::CommentOnDocumentsOfEventSeries,
        Organization::class => Ability::CommentOnDocumentsOfOrganizations,
    ];

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Document $document): Response
    {
        if (
            /**
             * Every user can view the comments of document if
             * - they are responsible for the event, event series or organization the document was uploaded to.
             * - they uploaded the document.
             */
            $user->is($document->uploadedByUser)
            || $user->isResponsibleFor($document->reference)
        ) {
            return $this->allow();
        }

        return $this->requireOneAbilityOf($user, array_filter([
            self::VIEW_COMMENTS_ON_DOCUMENTS_ABILITIES[$document->reference::class] ?? null,
            self::COMMENT_ON_DOCUMENTS_ABILITIES[$document->reference::class] ?? null,
        ], static fn (?Ability $ability) => $ability !== null));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentReview $documentReview): Response
    {
        if ($user->is($documentReview->user)) {
            // Every user can view the comments they wrote.
            return $this->allow();
        }

        return $this->viewAny($user, $documentReview->document);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Document $document): Response
    {
        if (
            /**
             * Every user can add comments to document if
             * - they are responsible for the event, event series or organization the document was uploaded to.
             * - they uploaded the document.
             */
            $user->is($document->uploadedByUser)
            || $user->isResponsibleFor($document->reference)
        ) {
            return $this->allow();
        }

        return $this->requireAbilityForDocument(self::COMMENT_ON_DOCUMENTS_ABILITIES, $user, $document);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentReview $documentReview): Response
    {
        if ($documentReview->user->is($user)) {
            // Reviewers can only update the comments of their own reviews.
            return $this->requireAbilityForDocument(self::COMMENT_ON_DOCUMENTS_ABILITIES, $user, $documentReview->document);
        }

        return $this->deny();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentReview $documentReview): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DocumentReview $documentReview): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DocumentReview $documentReview): Response
    {
        return $this->deny();
    }
}
