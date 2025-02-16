<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property-read ?string $phone_link {@see self::phoneLink()}
 */
trait HasPhone
{
    public function phoneLink(): Attribute
    {
        return Attribute::get(function () {
            if (!isset($this->phone)) {
                return null;
            }

            $phone = preg_replace('/[^\d\+]/', '', $this->phone);
            if (!str_starts_with($phone, '+')) {
                $phone = '+49' . substr($phone, str_starts_with($phone, '0') ? 1 : 0);
            }

            return 'tel:' . $phone;
        });
    }
}
