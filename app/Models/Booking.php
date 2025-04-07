<?php

namespace App\Models;

use App\Enums\DeletedFilter;
use App\Enums\FilterValue;
use App\Enums\FormElementType;
use App\Enums\PaymentStatus;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\HasAddress;
use App\Models\Traits\HasFullName;
use App\Models\Traits\HasPhone;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

/**
 * @property-read int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property ?string $phone
 * @property ?Carbon $date_of_birth
 * @property ?Carbon $booked_at
 * @property ?float $price
 * @property ?Carbon $paid_at
 * @property ?string $comment
 * @property ?Carbon $deleted_at
 *
 * @property-read int $booking_option_id
 *
 * @property-read ?float $age {@see self::age()}
 * @property-read string $file_name {@see self::fileName()}
 * @property-read string $file_name_for_pdf_download {@see self::fileNameForPdfDownload()}
 * @property-read ?Carbon $payment_deadline {@see self::paymentDeadline()}
 *
 * @property-read ?User $bookedByUser {@see self::bookedByUser()}
 * @property-read BookingOption $bookingOption {@see self::bookingOption()}
 * @property-read Collection|FormFieldValue[] $formFieldValues {@see self::formFieldValues()}
 * @property-read Collection|Group[] $groups {@see self::groups()}
 */
class Booking extends Model
{
    use BuildsQueryFromRequest;
    use HasAddress;
    use HasFactory;
    use HasFullName;
    use HasPhone;
    use SoftDeletes;

    protected $casts = [
        'booking_option_id' => 'integer',
        'date_of_birth' => 'date',
        'booked_at' => 'datetime',
        'price' => 'float',
        'paid_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'date_of_birth',
        'booked_at',
        'paid_at',
        'comment',
    ];

    protected function age(): Attribute
    {
        return Attribute::get(fn () => $this->date_of_birth?->diffInYears())
            ->shouldCache();
    }

    protected function fileName(): Attribute
    {
        return Attribute::get(fn () => Str::slug(implode('-', [
            $this->id,
            $this->first_name,
            $this->last_name,
        ])));
    }

    protected function fileNameForPdfDownload(): Attribute
    {
        return Attribute::get(fn () => $this->prefixFileNameWithGroup($this->file_name . '.pdf'));
    }

    protected function paymentDeadline(): Attribute
    {
        return Attribute::get(fn () => isset($this->price) ? $this->bookingOption->getPaymentDeadline($this->booked_at) : null)
            ->shouldCache();
    }

    public function bookedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function bookingOption(): BelongsTo
    {
        return $this->belongsTo(BookingOption::class, 'booking_option_id');
    }

    public function formFieldValues(): HasMany
    {
        return $this->hasMany(FormFieldValue::class)
                    ->with('formField');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps();
    }

    public function scopeGroup(Builder $query, Group|int $group): Builder
    {
        $groupId = is_int($group) ? $group : $group->id;

        return $query->whereHas(
            'groups',
            fn (Builder $groupQuery) => $groupQuery->where('group_id', '=', $groupId)
        );
    }

    public function scopePaymentStatus(Builder $query, int|PaymentStatus $paymentStatus): Builder
    {
        $payment = is_int($paymentStatus)
            ? $paymentStatus
            : $paymentStatus->value;

        return match ($payment) {
            PaymentStatus::Paid->value => $query->whereNotNull('paid_at')->orWhereNull('price'),
            PaymentStatus::NotPaid->value => $query->whereNull('paid_at')->whereNotNull('price'),
            default => $query,
        };
    }

    public function scopeSearchAll(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['first_name', 'last_name'], true, ...$searchTerms);
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        if (!$this->fill($validatedData)->save()) {
            return false;
        }

        foreach ($this->bookingOption->formFields ?? [] as $field) {
            if ($field->type->isStatic()) {
                continue;
            }

            if (!isset($field->column)) {
                $value = $validatedData[$field->input_name] ?? null;
                if ($field->type === FormElementType::File) {
                    if (!($value instanceof UploadedFile)) {
                        // Don't update value if no file has been uploaded during the validated request.
                        continue;
                    }

                    $fileName = str_replace(' ', '', implode('-', [
                        $this->id,
                        $this->first_name,
                        $this->last_name,
                        $field->id,
                    ])) . '.' . $value->extension();
                    $path = $value->storeAs($this->bookingOption->getFilePath(), $fileName);
                    if ($path === false) {
                        $errors = Arr::wrap(Session::get('error'));
                        $errors[] = __('Could not save file for :name.', [
                            'name' => $field->name,
                        ]);
                        Session::flash('error', $errors);
                    } else {
                        $value = $path;
                    }
                }

                if (!$this->setFieldValue($field, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function prefixFileNameWithGroup(string $baseFileName): string
    {
        $downloadFileName = $baseFileName;
        $group = $this->getGroup($this->bookingOption->event);
        if (isset($group)) {
            $downloadFileName = Str::slug($group->name) . '-' . $downloadFileName;
        }

        return $downloadFileName;
    }

    public function prepareMailMessage(): MailMessage
    {
        $mail = new MailMessage();
        $mail->greeting($this->bookedByUser->greeting ?? $this->greeting);

        if (isset($this->bookedByUser) && $this->bookedByUser->email !== $this->email) {
            $mail->cc($this->bookedByUser->email);
        }

        $organization = $this->bookingOption->event->organization;
        if (isset($organization->email)) {
            $mail->bcc($organization->email)
                ->replyTo($organization->email, $organization->name);
        }

        return $mail;
    }

    public function getGroup(Event $event): ?Group
    {
        return $this->groups->first(fn (Group $group) => $group->event_id === $event->id);
    }

    public function setFieldValue(FormField $formField, mixed $value): bool
    {
        $formFieldValue = $this->formFieldValues->loadMissing(['formField'])->first(
            static fn (FormFieldValue $existingFormFieldValue) => $existingFormFieldValue->formField->is($formField)
        );

        if ($formFieldValue === null) {
            /** @var FormFieldValue $formFieldValue */
            $formFieldValue = $this->formFieldValues()->make();
            $formFieldValue->formField()->associate($formField);
        }

        $formFieldValue->forceCast();
        $formFieldValue->value = $value;
        return $formFieldValue->save();
    }

    public function getFieldValue(FormField $formField): mixed
    {
        if (isset($formField->column)) {
            return $this->{$formField->column} ?? null;
        }

        /** @var ?FormFieldValue $fieldValue */
        $fieldValue = $this->formFieldValues
            ->first(
                static fn (FormFieldValue $formFieldValue) => $formFieldValue->formField->column === null
                    && $formFieldValue->formField->is($formField)
            );
        return $fieldValue?->getRealValue();
    }

    public function getFieldValueAsText(FormField $formField): ?string
    {
        $value = $this->getFieldValue($formField);

        if (isset($value)) {
            if (is_array($value)) {
                return implode(',', $value);
            }

            if ($formField->isSingleCheckbox()) {
                $value = $value ? __('Yes') : __('No');
            }

            $value = match ($formField->type) {
                FormElementType::Date => formatDate($value),
                FormElementType::DateTime => formatDateTime($value),
                default => $value,
            };
        }

        return $value;
    }

    public function storePdfFile(): string
    {
        $directoryPath = $this->bookingOption->getFilePath();
        Storage::disk('local')->makeDirectory($directoryPath);

        $filePath = $directoryPath . '/' . $this->file_name . '.pdf';
        Pdf::loadView('bookings.booking_show_pdf', [
            'booking' => $this->loadMissing([
                'bookingOption.formFields',
            ]),
        ])
            ->addInfo([
                'Author' => config('app.owner'),
                'Title' => implode(' ', [
                    $this->bookingOption->name,
                    $this->first_name,
                    $this->last_name,
                ]),
            ])
            ->save(Storage::disk('local')->path($filePath));

        return $filePath;
    }

    /**
     * @return array<int, AllowedFilter>
     */
    public static function allowedFilters(): array
    {
        return [
            /** @see self::scopeSearchAll() */
            AllowedFilter::scope('search', 'searchAll'),
            /** @see self::scopeGroup() */
            AllowedFilter::scope('group_id', 'group')
                ->ignore(FilterValue::All->value),
            /** @see self::scopePaymentStatus() */
            AllowedFilter::scope('payment_status', 'paymentStatus')
                ->ignore(FilterValue::All->value),
            AllowedFilter::trashed()
                ->default(DeletedFilter::HideDeleted->value),
        ];
    }

    /**
     * @param Collection<int, self> $bookings
     * @return Collection<int, self>
     */
    public static function sort(Collection $bookings, string $sort): Collection
    {
        $column = ltrim($sort, '-');
        if ($column === 'name') {
            $column = fn (self $booking) => $booking->last_name . ', ' . $booking->first_name;
        }

        return $bookings->sortBy($column, descending: str_starts_with($sort, '-'));
    }

    public static function sortOptions(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Time of booking'), AllowedSort::field('booked_at'), true)
            ->addBothDirections(__('Time of last update'), AllowedSort::field('updated_at'))
            ->addBothDirections(
                __('Name'),
                AllowedSort::callback(
                    'name',
                    fn (Builder $query, bool $descending, string $property) => $query
                        ->orderBy('last_name', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                        ->orderBy('first_name', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                ),
                true
            )
            ->addBothDirections(__('Date of birth'), AllowedSort::field('date_of_birth'));
    }
}
