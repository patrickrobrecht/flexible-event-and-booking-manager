<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\BuildsResource;
use App\Models\Event;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Event
 */
class EventResource extends JsonResource
{
    use BuildsResource;

    public function relationsToArray(): array
    {
        return [
            'event_series' => new EventSeriesResource($this->whenLoaded('eventSeries')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'parent_event' => new EventResource($this->whenLoaded('parentEvent')),
            'sub_events' => EventSeriesResource::collection($this->whenLoaded('subEvents')),
        ];
    }
}
