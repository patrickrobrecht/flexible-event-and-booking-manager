<?php

namespace App\Models;

use App\Models\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;

/**
 * @property-read int $id
 * @property string $title
 * @property ?string $description
 * @property string $path
 *
 * @property-read HasDocuments|Event $reference {@see self::reference()}
 */
class Document extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        /** @var UploadedFile $file */
        $file = $validatedData['file'] ?? null;
        if ($file) {
            $this->path = $file->storeAs($this->reference->getStoragePath() . '/documents', $file->getClientOriginalName());
        }

        return $this->save();
    }
}
