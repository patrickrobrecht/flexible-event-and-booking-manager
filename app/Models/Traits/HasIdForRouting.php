<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasIdForRouting
{
    public function resolveRouteBinding($value, $field = null): ?static
    {
        try {
            return self::query()->findOrFail($value);
        } catch (ModelNotFoundException $exception) {
            // Set $value as model IDs for proper exception handling.
            throw $exception->setModel($exception->getModel(), [$value]);
        }
    }
}
