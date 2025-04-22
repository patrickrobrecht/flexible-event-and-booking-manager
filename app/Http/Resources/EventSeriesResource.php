<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\BuildsResource;
use App\Models\EventSeries;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EventSeries
 */
class EventSeriesResource extends JsonResource
{
    use BuildsResource;

    /**
     * @return array<string, mixed>
     */
    public function relationsToArray(): array
    {
        return [
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'parent_event_series' => new EventSeriesResource($this->whenLoaded('parentEventSeries')),
            'sub_event_series' => self::collection($this->whenLoaded('subEventSeries')),
        ];
    }
}
