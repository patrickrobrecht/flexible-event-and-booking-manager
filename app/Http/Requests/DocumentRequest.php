<?php

namespace App\Http\Requests;

use App\Enums\ApprovalStatus;
use App\Enums\FileType;
use App\Http\Requests\Traits\ValidatesFiles;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Stringable;

/**
 * @property ?Document $document
 * @property-read ?Event $event
 * @property-read ?EventSeries $event_series
 * @property-read ?Organization $organization
 */
class DocumentRequest extends FormRequest
{
    use ValidatesFiles;

    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        $fileExtensions = FileType::extensions();
        $mimeTypes = [
            ...self::getMimeTypesFromExtensions($fileExtensions),
            'application/octet-stream',
            'application/x-empty',
        ];

        /** @var Event|EventSeries|Organization $reference */
        $reference = $this->getReference();

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('documents', 'title')
                    ->where('reference_type', $reference::class)
                    ->where('reference_id', $reference->id)
                    ->ignore($this->document->id ?? null),
            ],
            'description' => [
                'nullable',
                'string',
                'different:title',
            ],
            'file' => [
                $this->routeIs('*.documents.store') ? 'required' : 'nullable',
                'file',
                'extensions:' . implode(',', $fileExtensions),
                'mimetypes:' . implode(',', $mimeTypes),
                self::getMaxFileSizeRule(),
            ],
            'approval_status' => [
                (
                    $this->routeIs('*.documents.store')
                    && $this->user()?->can('approve', Document::class)
                ) || (
                    $this->routeIs('documents.update')
                    && $this->user()?->can('approve', $this->document)
                )
                    ? 'required'
                    : 'prohibited',
                ApprovalStatus::rule(),
            ],
        ];
    }

    private function getReference(): Model
    {
        if ($this->routeIs('documents.update')) {
            /** @phpstan-ignore-next-line property.nonObject */
            return $this->document->reference;
        }

        if ($this->routeIs('events.documents.store')) {
            /** @phpstan-ignore-next-line return.type */
            return $this->event;
        }

        if ($this->routeIs('event-series.documents.store')) {
            /** @phpstan-ignore-next-line return.type */
            return $this->event_series;
        }

        if ($this->routeIs('organizations.documents.store')) {
            /** @phpstan-ignore-next-line return.type */
            return $this->organization;
        }

        throw new InvalidArgumentException("{$this->route()?->getName()} not supported");
    }
}
