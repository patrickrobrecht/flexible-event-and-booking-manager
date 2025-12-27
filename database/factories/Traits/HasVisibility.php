<?php

namespace Database\Factories\Traits;

use App\Enums\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @mixin Factory
 *
 * @phpstan-ignore-next-line missingType.generics
 */
trait HasVisibility
{
    public function visibility(?Visibility $visibility): static
    {
        if ($visibility === null) {
            return $this;
        }

        return $this->state(fn (array $attributes) => [
            'visibility' => $visibility,
        ]);
    }
}
