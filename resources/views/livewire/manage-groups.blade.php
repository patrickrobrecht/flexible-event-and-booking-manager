<div x-data="{
    showModal: false,
    groupIdToDelete: null,
    groupNameToDelete: null,
}" x-init="() => {
    $watch('showModal', value => {
        let modalElement = $refs.deleteModal;
        let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        value ? modal.show() : modal.hide();
    });
}">
    @include('layouts.alerts')

    <div class="row">
        <div class="col-12 col-md-6 col-xl-3">
            <x-bs::form.field name="sort" type="select"
                              :options="\App\Models\Booking::sortOptions()->getNamesWithLabels()"
                              wire:model.live="sort">{{ __('Sorting') }}</x-bs::form.field>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3"
             x-on:dragover.prevent="allowDrop($event)"
             x-on:drop.prevent="drop($event, -1)">
            <div class="card shadow-sm rounded">
                <div class="card-header bg-danger text-bg-danger">
                    <h2 class="card-title">{{ __('Without group') }}</h2>
                </div>
                <x-bs::list :flush="true" data-group-id="-1">
                    @foreach(($bookingsWithoutGroup ?? \Illuminate\Database\Eloquent\Collection::empty()) as $booking)
                        @include('livewire.manage-groups-booking')
                    @endforeach
                </x-bs::list>
            </div>
        </div>

        @foreach($groups as $group)
            @php
                /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
                $bookings = $group['bookings'] ?? \Illuminate\Database\Eloquent\Collection::empty();
            @endphp
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3"
                 x-on:dragover.prevent="allowDrop($event)"
                 x-on:drop.prevent="drop($event, {{ $group->id }})">
                <div class="card">
                    <div @class([
                        'card-header',
                        $bookings->isEmpty() ? 'bg-secondary text-bg-secondary' : 'bg-primary text-bg-primary',
                    ])>
                        <h2 class="card-title">{{ $group->name }}</h2>
                        @isset($group->description)
                            <p class="card-subtitle">{{ $group->description }}</p>
                        @endisset
                    </div>
                    <x-bs::list :flush="true" data-group-id="{{ $group->id }}">
                        @foreach($bookings as $booking)
                            @include('livewire.manage-groups-booking')
                        @endforeach
                    </x-bs::list>
                    <div class="card-body">
                        @can('update', $group)
                            <x-bs::button type="button" wire:click="editGroup({{ $group->id }})">
                                <i class="fa fa-edit"></i> {{ __('Edit') }}
                            </x-bs::button>
                        @endcan
                        @can('forceDelete', $group)
                            <x-bs::button type="button" variant="danger" x-on:click="showModal = true; groupIdToDelete = {{ $group->id }}; groupNameToDelete = '{{ $group->name }}';">
                                <i class="fa fa-minus-circle"></i> {{ __('Delete') }}
                            </x-bs::button>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        function dragStart(event, bookingId) {
            event.dataTransfer.setData('bookingId', bookingId);
        }

        function allowDrop(event) {
            event.preventDefault();
        }

        function drop(event, groupId) {
            event.preventDefault();
            let bookingId = event.dataTransfer.getData('bookingId');
            @this.call('moveBooking', bookingId, groupId);
        }
    </script>
    {{-- Delete Confirmation Modal --}}
    <div x-ref="deleteModal" id="deleteConfirmationModal" class="modal fade" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteConfirmationModalLabel" x-text="groupNameToDelete"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="showModal = false;"></button>
                </div>
                <div class="modal-body">{{ __('Are you sure you want to delete this group?') }}</div>
                <div class="modal-footer">
                    <x-bs::button type="button" data-bs-dismiss="modal" @click="showModal = false;"><i class="fa fa-window-close"></i> {{ __('Cancel') }}</x-bs::button>
                    <x-bs::button type="button" variant="danger" @click="showModal = false; $wire.call('deleteGroup', groupIdToDelete);">
                        <i class="fa fa-minus-circle"></i> {{ __('Delete') }}
                    </x-bs::button>
                </div>
            </div>
        </div>
    </div>
</div>
