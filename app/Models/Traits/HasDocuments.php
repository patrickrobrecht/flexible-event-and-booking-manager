<?php

namespace App\Models\Traits;

use App\Enums\FileType;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property-read Collection<int, Document> $documents {@see self::documents()}
 * @property-read Collection<int, Document> $images {@see self::images()}
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

    protected function images(): Attribute
    {
        return Attribute::get(
            fn () => $this->relationLoaded('documents')
                ? $this->documents->where('file_type', '=', FileType::Image)
                : $this->documents()->where('file_type', '=', FileType::Image)->get()
        )->shouldCache();
    }

    public function scopeDocument(Builder $query, int|string $documentId): Builder
    {
        return $this->scopeRelation($query, $documentId, 'documents', fn (Builder $q) => $q->where('document_id', '=', $documentId));
    }

    public function getDocumentStoragePath(): string
    {
        return $this->getStoragePath() . '/documents';
    }

    public function hasImages(): bool
    {
        return $this->images->isNotEmpty();
    }

    abstract public function getRoute(): string;

    abstract public function getRouteForGallery(): string;

    abstract public function getStoragePath(): string;
}
