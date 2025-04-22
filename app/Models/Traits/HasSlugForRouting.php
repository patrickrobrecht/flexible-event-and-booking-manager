<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin Model
 */
trait HasSlugForRouting
{
    use HasSlug;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param int|string $value
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        try {
            return self::query()
                 ->where('slug', '=', $value)
                 ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            // Set $value as model IDs for proper exception handling.
            throw $exception->setModel($exception->getModel(), [$value]);
        }
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }
}
