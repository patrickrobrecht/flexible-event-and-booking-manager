<?php

namespace App\Models\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read Collection|Document[] $documents {@see self::documents()}
 *
 * @mixin Model
 */
trait HasDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'reference')
            ->orderBy('title');
    }

    abstract public function getRoute(): string;

    abstract public function getStoragePath(): string;
}
