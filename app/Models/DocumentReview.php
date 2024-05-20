<?php

namespace App\Models;

use App\Options\ApprovalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property string $comment
 * @property ?ApprovalStatus $approval_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Document $document {@see self::document()}
 * @property-read User $user {@see self::user()}
 */
class DocumentReview extends Model
{
    protected $casts = [
        'document_id' => 'integer',
        'user_id' => 'integer',
        'approval_status' => ApprovalStatus::class,
    ];

    protected $fillable = [
        'comment',
        'approval_status',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }
}
