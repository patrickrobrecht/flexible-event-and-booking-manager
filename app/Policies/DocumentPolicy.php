<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Http\Controllers\DocumentController;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use App\Policies\Traits\ChecksAbilitiesForDocuments;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class DocumentPolicy
{
    use ChecksAbilities;
    use ChecksAbilitiesForDocuments;

    public const array VIEW_DOCUMENTS_ABILITIES = [
        Event::class => Ability::ViewDocumentsOfEvents,
        EventSeries::class => Ability::ViewDocumentsOfEventSeries,
        Organization::class => Ability::ViewDocumentsOfOrganizations,
    ];

    public const array ADD_DOCUMENTS_ABILITIES = [
        Event::class => Ability::AddDocumentsToEvents,
        EventSeries::class => Ability::AddDocumentsToEventSeries,
        Organization::class => Ability::AddDocumentsToOrganizations,
    ];

    public const array EDIT_DOCUMENTS_ABILITIES = [
        Event::class => Ability::EditDocumentsOfEvents,
        EventSeries::class => Ability::EditDocumentsOfEventSeries,
        Organization::class => Ability::EditDocumentsOfOrganizations,
    ];

    public const array DELETE_DOCUMENTS_ABILITIES = [
        Event::class => Ability::DestroyDocumentsOfEvents,
        EventSeries::class => Ability::DestroyDocumentsOfEventSeries,
        Organization::class => Ability::DestroyDocumentsOfOrganizations,
    ];

    public const array CHANGE_APPROVAL_STATUS_OF_DOCUMENTS_ABILITIES = [
        Event::class => Ability::ChangeApprovalStatusOfDocumentsOfEvents,
        EventSeries::class => Ability::ChangeApprovalStatusOfDocumentsOfEventSeries,
        Organization::class => Ability::ChangeApprovalStatusOfDocumentsOfOrganizations,
    ];

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Event|EventSeries|Organization|null $reference = null): Response
    {
        if ($reference === null) {
            /** List of documents filtered in {@see DocumentController::index()}. */
            return $this->requireOneAbilityOf($user, self::VIEW_DOCUMENTS_ABILITIES);
        }

        return $this->requireAbilityOrResponsibleUser(self::VIEW_DOCUMENTS_ABILITIES, $user, $reference);
    }

    /**
     * @param array<class-string<Model>, Ability> $abilitiesPerReferenceType
     */
    private function requireAbilityOrResponsibleUser(
        array $abilitiesPerReferenceType,
        User $user,
        Event|EventSeries|Organization $reference
    ): Response {
        $abilityForReference = $abilitiesPerReferenceType[$reference::class] ?? null;
        if ($abilityForReference === null) {
            return $this->deny();
        }

        $abilityResponse = $this->requireAbility($user, $abilityForReference);
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
        return $this->requireAbilityOrResponsibleUser(self::ADD_DOCUMENTS_ABILITIES, $user, $reference);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): Response
    {
        return $this->requireAbilityOrResponsibleUser(self::EDIT_DOCUMENTS_ABILITIES, $user, $document->reference);
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
        return $this->requireAbilityForDocument(self::DELETE_DOCUMENTS_ABILITIES, $user, $document);
    }

    /**
     * Determine whether the user can change the approval status of the document.
     */
    public function approve(User $user, ?Document $document = null): Response
    {
        if ($document === null) {
            return $this->deny();
        }

        return $this->requireAbilityForDocument(self::CHANGE_APPROVAL_STATUS_OF_DOCUMENTS_ABILITIES, $user, $document);
    }
}
