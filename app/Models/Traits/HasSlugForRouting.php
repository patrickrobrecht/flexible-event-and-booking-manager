<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
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

    public function resolveRouteBinding($value, $field = null): ?static
    {
        /** @var ?static $model */
        $model = self::query()
             ->where('slug', '=', $value)
             ->firstOrFail();

        return $model;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }
}
