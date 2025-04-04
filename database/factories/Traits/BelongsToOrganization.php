<?php

namespace Database\Factories\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @mixin Factory
 */
trait BelongsToOrganization
{
    public function forOrganization(?Organization $organization = null): static
    {
        return $this->for($organization ?? Organization::factory()->forLocation());
    }
}
