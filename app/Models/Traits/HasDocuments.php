<?php

namespace App\Models\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
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
    use FiltersByRelationExistence;

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'reference')
            ->orderBy('title');
    }

    public function scopeDocument(Builder $query, int|string $documentId): Builder
    {
        return $this->scopeRelation($query, $documentId, 'documents', fn (Builder $q) => $q->where('document_id', '=', $documentId));
    }

    public function getDocumentStoragePath(): string
    {
        return $this->getStoragePath() . '/documents';
    }

    abstract public function getRoute(): string;

    abstract public function getStoragePath(): string;
}
