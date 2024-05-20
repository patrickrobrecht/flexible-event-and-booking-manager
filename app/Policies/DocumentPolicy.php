<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
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
    public function viewAny(User $user, Event|EventSeries|Organization|null $reference = null): Response
    {
        if ($reference === null) {
            return $this->requireAbility($user, Ability::ViewDocuments);
        }

        if ($user->cannot('view', $reference)) {
            return $this->deny();
        }

        if ($user->hasAbility(Ability::ViewDocuments)) {
            // ViewDocuments grants access to all documents.
            return $this->allow();
        }

        return $this->requireAbilityOrResponsibleUser(
            match ($reference::class) {
                Event::class => $this->requireAbility($user, Ability::ViewDocumentsOfEvents),
                EventSeries::class => $this->requireAbility($user, Ability::ViewDocumentsOfEventSeries),
                Organization::class => $this->requireAbility($user, Ability::ViewDocumentsOfOrganizations),
                default => $this->deny(),
            },
            $user,
            $reference
        );
    }

    private function requireAbilityOrResponsibleUser(
        Response $abilityResponse,
        User $user,
        Event|EventSeries|Organization $reference
    ): Response {
        if ($abilityResponse->allowed()) {
            return $abilityResponse;
        }

        // Responsible users can always access documents of their events, event series and organizations.
        return $this->response($user->isResponsibleFor($reference));
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
    public function create(User $user, Event|EventSeries|Organization $reference): Response
    {
        if ($user->cannot('view', $reference)) {
            return $this->deny();
        }

        return $this->requireAbilityOrResponsibleUser(
            match ($reference::class) {
                Event::class => $this->requireAbility($user, Ability::AddDocumentsToEvents),
                EventSeries::class => $this->requireAbility($user, Ability::AddDocumentsToEventSeries),
                Organization::class => $this->requireAbility($user, Ability::AddDocumentsToOrganizations),
                default => $this->deny(),
            },
            $user,
            $reference
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): Response
    {
        if ($user->cannot('view', $document->reference)) {
            return $this->deny();
        }

        return $this->requireAbilityOrResponsibleUser(
            match ($document->reference::class) {
                Event::class => $this->requireAbility($user, Ability::EditDocumentsOfEvents),
                EventSeries::class => $this->requireAbility($user, Ability::EditDocumentsOfEventSeries),
                Organization::class => $this->requireAbility($user, Ability::EditDocumentsOfOrganizations),
                default => $this->deny(),
            },
            $user,
            $document->reference
        );
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

        return match ($document->reference::class) {
            Event::class => $this->requireAbility($user, Ability::DeleteDocumentsOfEvents),
            EventSeries::class => $this->requireAbility($user, Ability::DeleteDocumentsOfEventSeries),
            Organization::class => $this->requireAbility($user, Ability::DeleteDocumentsOfOrganizations),
            default => $this->deny(),
        };
    }

    /**
     * Determine whether the user can change the approval status of the document.
     */
    public function approve(User $user, ?Document $document = null): Response
    {
        return $this->requireAbility($user, Ability::ChangeApprovalStatusOfDocuments);
    }
}
