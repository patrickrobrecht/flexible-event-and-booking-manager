<?php

namespace App\Models;

use App\Enums\MaterialStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read int $id
 * @property MaterialStatus $material_status
 * @property ?int $stock
 * @property ?string $remarks
 */
class MaterialStorageLocation extends Pivot
{
    /** @var array<int, string> */
    public const array PIVOT_COLUMNS = [
        'id',
        'material_status',
        'stock',
        'remarks',
    ];

    protected $casts = [
        'material_status' => MaterialStatus::class,
    ];

    protected $fillable = self::PIVOT_COLUMNS;

    public $incrementing = true;
}
