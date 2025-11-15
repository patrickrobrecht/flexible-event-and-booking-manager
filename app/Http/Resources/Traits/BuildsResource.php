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
     *
     * @return array<string, mixed>
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function relationsToArray(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
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
