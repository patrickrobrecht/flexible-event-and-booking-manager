<?php

namespace App\Models;

use App\Models\Traits\HasNameAndDescription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read int $id
 * @property-read int $group_id
 * @property string $name
 * @property ?string $description
 *
 * @property-read Collection|Booking[] $bookings {@see self::bookings()}
 * @property-read Event $event {@see self::event()}
 */
class Group extends Model
{
    use HasFactory;
    use HasNameAndDescription;
    use HasTimestamps;

    protected $casts = [
        'event_id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'description',
    ];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class)
            ->withTimestamps();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
