<?php

namespace App\Policies\Traits;

use App\Enums\Ability;
use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

trait ChecksAbilitiesForDocuments
{
    /**
     * @param array<class-string<Model>, Ability> $abilitiesPerReferenceType
     */
    protected function requireAbilityForDocument(array $abilitiesPerReferenceType, User $user, Document $document): Response
    {
        $abilityForReference = $abilitiesPerReferenceType[$document->reference::class] ?? null;

        return $abilityForReference === null
            ? $this->deny()
            : $this->requireAbility($user, $abilityForReference);
    }
}
