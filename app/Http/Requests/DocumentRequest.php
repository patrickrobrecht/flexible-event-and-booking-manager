<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesFiles;
use App\Models\Document;
use App\Models\Traits\HasDocuments;
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

    public const FILE_EXTENSIONS = [
        'doc',
        'docx',
        'jpeg',
        'jpg',
        'md',
        'odp',
        'ods',
        'odt',
        'pdf',
        'png',
        'ppt',
        'pptx',
        'svg',
        'txt',
        'xls',
        'xlsx',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $mimeTypes = [
            ...self::getMimeTypesFromExtensions(self::FILE_EXTENSIONS),
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
                $this->routeIs('create') ? 'required' : 'nullable',
                'file',
                'extensions:' . implode(',', self::FILE_EXTENSIONS),
                'mimetypes:' . implode(',', $mimeTypes),
                self::getMaxFileSizeRule(),
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

        throw new \InvalidArgumentException("{$this->route()->getName()} not supported");
    }

    public static function getAllowedExtensionsForHtmlAccept(): string
    {
        return self::getExtensionsForHtmlAccept(self::FILE_EXTENSIONS);
    }
}
