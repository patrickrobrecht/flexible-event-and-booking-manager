<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\HasDocuments;
use App\Options\ApprovalStatus;
use App\Options\FileType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

/**
 * @property-read int $id
 * @property string $title
 * @property ?string $description
 * @property string $path
 * @property FileType $file_type
 * @property ApprovalStatus $approval_status
 *
 * @property-read HasDocuments|Event $reference {@see self::reference()}
 * @property-read User $uploadedByUser {@see self::uploadedByUser()}
 */
class Document extends Model
{
    use BuildsQueryFromRequest;

    protected $casts = [
        'file_type' => FileType::class,
        'uploaded_by_user_id' => 'integer',
        'approval_status' => ApprovalStatus::class,
    ];

    protected $fillable = [
        'title',
        'description',
        'approval_status',
    ];

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        /** @var UploadedFile $file */
        $file = $validatedData['file'] ?? null;
        if ($file) {
            $this->file_type = FileType::tryFromExtension($file->getClientOriginalExtension()) ?? FileType::Text;
            $this->uploadedByUser()->associate(Auth::user());

            $targetDirectory = $this->reference->getDocumentStoragePath();
            if ($this->exists) {
                $this->path = $file->storeAs($targetDirectory, $this->id . '-' . $file->getClientOriginalName());
            } else {
                $tempFileName = 'tmp-' . Carbon::now()->format('Ymd-His') . '-' . $file->getClientOriginalName();
                $this->path = $file->storeAs($targetDirectory, $tempFileName);
                $this->save();

                $newFileName = $this->id . '-' . $file->getClientOriginalName();
                Storage::move($targetDirectory . '/' . $tempFileName, $targetDirectory . '/' . $newFileName);
                $this->path = $targetDirectory . '/' . $newFileName;
            }
        }

        return $this->save();
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('title'),
            AllowedFilter::exact('file_type'),
            AllowedFilter::exact('approval_status'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Title'), AllowedSort::field('title'), true)
            ->merge(self::sortOptionsForTimeStamps());
    }
}
