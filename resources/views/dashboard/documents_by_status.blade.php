@php
    use App\Enums\ApprovalStatus;
    use Illuminate\Database\Eloquent\Collection;

    /** @var Collection<int, int> $documentsByStatus */
@endphp
<x-bs::list>
    @foreach(ApprovalStatus::cases() as $approvalStatus)
        @php
            $count = $documentsByStatus[$approvalStatus->value] ?? 0;
            $link = route($routeName, [
                'filter[approval_status]' => $approvalStatus->value,
            ]);
        @endphp
        <x-bs::list.item container="a" href="{{ $link }}" variant="action">
            <x-badge.enum :case="$approvalStatus"/>
            <x-slot:end>
                <span @class([
                    'text-danger fw-bold' => $approvalStatus->isNotDecided() && $count > 0,
                ])>{{ formatInt($count) }}</span>
            </x-slot:end>
        </x-bs::list.item>
    @endforeach
</x-bs::list>
