<?php

namespace Database\Factories\Traits;

use App\Enums\Visibility;

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
