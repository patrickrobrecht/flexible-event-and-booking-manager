<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\Searchable;
use App\Options\ApprovalStatus;
use App\Options\FileType;
use App\Options\FilterValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read string $file_name_from_title {@see self::fileNameFromTitle()}
 *
 * @property-read Collection|DocumentReview[] $documentReviews {@see self::documentReviews()}
 * @property-read HasDocuments|Event $reference {@see self::reference()}
 * @property-read User $uploadedByUser {@see self::uploadedByUser()}
 */
class Document extends Model
{
    use BuildsQueryFromRequest;
    use Searchable;

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

    public function fileNameFromTitle(): Attribute
    {
        return Attribute::get(
            fn () => preg_replace('/[^A-Za-z0-9äöüÄÖÜß_\-]/u', '', str_replace(' ', '-', $this->title))
                     . '.' . pathinfo($this->path, PATHINFO_EXTENSION)
        );
    }

    public function documentReviews(): HasMany
    {
        return $this->hasMany(DocumentReview::class)
            ->orderBy('created_at');
    }

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
        if ($this->approval_status === null) {
            // Set the approval status to the default value if not contained in the request.
            $this->approval_status = ApprovalStatus::WaitingForApproval;
        }

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

    public function scopeSearchTitleAndDescription(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['title', 'description'], true, ...$searchTerms);
    }

    public static function allowedFilters(): array
    {
        return [
            /** @see self::scopeSearchTitleAndDescription() */
            AllowedFilter::scope('search', 'searchTitleAndDescription'),
            AllowedFilter::exact('file_type')
                ->ignore(FilterValue::All->value),
            AllowedFilter::exact('approval_status')
                ->ignore(FilterValue::All->value),
        ];
    }

    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with at least one document'),
            FilterValue::Without->value => __('without documents'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Title'), AllowedSort::field('title'), true)
            ->merge(self::sortOptionsForTimeStamps());
    }
}
