<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\BuildsResource;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Organization
 */
class OrganizationResource extends JsonResource
{
    use BuildsResource;

    /**
     * @return array<string, mixed>
     */
    public function relationsToArray(): array
    {
        return [
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}
