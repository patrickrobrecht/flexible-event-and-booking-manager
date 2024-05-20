<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesFiles;
use App\Models\Document;
use App\Models\Traits\HasDocuments;
use App\Options\ApprovalStatus;
use App\Options\FileType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?Document $document
 */
class DocumentRequest extends FormRequest
{
    use ValidatesFiles;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $fileExtensions = FileType::extensions();
        $mimeTypes = [
            ...self::getMimeTypesFromExtensions($fileExtensions),
            'application/octet-stream',
            'application/x-empty',
        ];

        /** @var HasDocuments $reference */
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
                    && $this->user()->can('approve', Document::class)
                ) || (
                    $this->routeIs('documents.update')
                    && $this->user()->can('approve', $this->document)
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
            return $this->document->reference;
        }

        if ($this->routeIs('events.documents.store')) {
            return $this->event;
        }

        if ($this->routeIs('event-series.documents.store')) {
            return $this->event_series;
        }

        if ($this->routeIs('organizations.documents.store')) {
            return $this->organization;
        }

        throw new \InvalidArgumentException("{$this->route()->getName()} not supported");
    }
}
