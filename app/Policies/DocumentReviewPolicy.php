<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class DocumentReviewPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, ?Document $document = null): Response
    {
        if (isset($document)) {
            if (
                /**
                 * Every user can view the reviews of document
                 * - they are responsible for the event, event series or organization the document was uploaded to.
                 * - they uploaded.
                 */
                $user->is($document->uploadedByUser)
                || $user->isResponsibleFor($document->reference)
            ) {
                return $this->allow();
            }
        }

        return $this->requireOneAbilityOf($user, [
            Ability::ViewCommentsOnDocuments,
            Ability::CommentOnDocuments,
        ]);
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
    public function create(User $user, ?Document $document = null): Response
    {
        return $this->requireAbility($user, Ability::CommentOnDocuments);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentReview $documentReview): Response
    {
        if ($documentReview->user->is($user)) {
            // Reviewers can only update the comments of their own reviews.
            return $this->requireAbility($user, Ability::CommentOnDocuments);
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
