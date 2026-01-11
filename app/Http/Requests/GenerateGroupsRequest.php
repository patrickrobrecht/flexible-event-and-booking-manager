<?php

namespace App\Http\Requests;

use App\Enums\GroupGenerationMethod;
use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property Event $event
 * @property-read int[] $booking_option_id
 * @property-read int[] $exclude_parent_group_id
 */
class GenerateGroupsRequest extends FormRequest
{
    protected $errorBag = 'generate';

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Cast IDs to integers.
            'booking_option_id' => array_map(
                'intval',
                /** @phpstan-ignore booleanAnd.rightAlwaysTrue */
                isset($this->booking_option_id) && is_array($this->booking_option_id)
                    ? $this->booking_option_id
                    : []
            ),
            'exclude_parent_group_id' => array_map(
                'intval',
                /** @phpstan-ignore booleanAnd.rightAlwaysTrue */
                isset($this->exclude_parent_group_id) && is_array($this->exclude_parent_group_id)
                    ? $this->exclude_parent_group_id
                    : []
            ),
        ]);
    }

    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        $selectedBookingOptionIds = array_intersect(
            /** @phpstan-ignore-next-line argument.type */
            $this->input('booking_option_id'),
            $this->event->getBookingOptions()->pluck('id')->toArray()
        );

        $bookingsCount = ($this->event->parentEvent ?? $this->event)
            ->bookingsConfirmed()
            ->whereIn('booking_option_id', $selectedBookingOptionIds)
            ->count();
        $maxGroupsCount = ceil($bookingsCount / 2) + 1;

        $parentGroupIds = isset($this->event->parentEvent)
            ? $this->event->parentEvent->groups()->pluck('id')->toArray()
            : [];

        return [
            'method' => [
                'required',
                GroupGenerationMethod::rule(),
            ],
            'groups_count' => [
                'integer',
                'gte:1',
                'lte:' . $maxGroupsCount,
            ],
            'booking_option_id' => [
                'array',
                'required',
            ],
            'booking_option_id.*' => [
                'integer',
                Rule::in($this->event->getBookingOptions()->pluck('id')),
            ],
            'exclude_parent_group_id' => [
                'array',
                count($parentGroupIds) === 0 ? 'prohibited' : 'nullable',
            ],
            'exclude_parent_group_id.*' => [
                'integer',
                Rule::in($parentGroupIds),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        /** @var array<string, string> $attributes */
        $attributes = [
            'booking_option_id' => __('Booking options'),
            'exclude_parent_group_id' => __('Exclude members of groups'),
        ];

        /** @phpstan-ignore-next-line argument.type */
        foreach (range(0, count($this->input('exclude_parent_group_id')) - 1) as $id) {
            $attributes['exclude_parent_group_id.' . $id] = __('Exclude members of groups');
        }

        return $attributes;
    }
}
