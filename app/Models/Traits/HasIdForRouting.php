<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasIdForRouting
{
    /**
     * @param int|string $value
     */
    /** @phpstan-ignore method.childParameterType */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        try {
            /** @phpstan-ignore-next-line */
            return self::query()->findOrFail($value);
        } catch (ModelNotFoundException $exception) {
            // Set $value as model IDs for proper exception handling.
            /** @phpstan-ignore argument.type */
            throw $exception->setModel($exception->getModel(), [$value]);
        }
    }
}
