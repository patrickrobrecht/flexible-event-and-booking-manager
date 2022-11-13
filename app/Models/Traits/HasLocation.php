<?php

namespace App\Models\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Location $location {@see HasLocation::location()}
 *
 * @mixin Model
 */
trait HasLocation
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
