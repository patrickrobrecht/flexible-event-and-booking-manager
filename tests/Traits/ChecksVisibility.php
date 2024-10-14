<?php

namespace Tests\Traits;

use App\Options\Visibility;

trait ChecksVisibility
{
    public static function visibilityProvider(): array
    {
        $data = [];
        foreach (Visibility::cases() as $visibility) {
            $data[] = [$visibility];
        }

        return $data;
    }
}
