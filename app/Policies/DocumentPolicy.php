<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Event $event): Response
    {
        if ($user->cannot('view', $event)) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::AddDocumentsForEvents);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): Response
    {
        return $this->viewAny($user, $document->reference);
    }

    public function download(User $user, Document $document): Response
    {
        return $this->view($user, $document);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Event $event): Response
    {
        if ($user->cannot('view', $event)) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::AddDocumentsForEvents);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): Response
    {
        if ($user->cannot('view', $document->reference)) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::UpdateDocumentsForEvents);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): Response
    {
        if ($user->cannot('view', $document->reference)) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::DeleteDocumentsForEvents);
    }
}
