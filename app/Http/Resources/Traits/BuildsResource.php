<?php

namespace App\Http\Resources\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

/**
 * @mixin JsonResource
 *
 * @property-read Model $resource
 */
trait BuildsResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return $this->cleanup(
            array_merge(
                $this->attributesToArray(),
                $this->relationsToArray()
            )
        );
    }

    public function attributesToArray(): array
    {
        return Arr::map(
            $this->resource->attributesToArray(),
            static function ($value, $key) {
                if (str_ends_with($key, '_count')) {
                    return (int) $value;
                }

                return $value;
            }
        );
    }

    public function relationsToArray(): array
    {
        return [];
    }

    protected function cleanup(array $array): array
    {
        return Arr::where(
            $array,
            static function ($value, $key) {
                return !str_ends_with($key, '_id')
                    && $key !== 'pivot'
                    && $value !== null;
            }
        );
    }
}
