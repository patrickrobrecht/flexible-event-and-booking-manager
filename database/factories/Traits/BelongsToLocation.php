<?php

namespace Database\Factories\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @mixin Factory
 */
trait BelongsToLocation
{
    public function forLocation(?Location $location = null): static
    {
        return $this->for($location ?? Location::factory());
    }
}
