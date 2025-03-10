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
    <div class="row my-3">
        <div class="col-12 col-md-6 col-lg-4 col-xxl-3">
            <x-bs::form.field name="sort" type="select" :options="\App\Models\Booking::sortOptions()->getNamesWithLabels()"
                              wire:model.live="sort" form="export-form">{{ __('Sorting') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-md-6 col-lg-5 col-xxl-7">
            <div class="form-label">{{ __('Booking options') }}</div>
            @foreach($event->getBookingOptions() as $bookingOption)
                @php
                    $checkBoxOptionForBookingOption = \Portavice\Bladestrap\Support\Options::fromArray([
                        $bookingOption->id => sprintf(
                            '<span class="text-nowrap"><a href="%s" target="_blank">%s</a> (%s)</span>',
                            route('bookings.index', [$event->parentEvent ?? $event, $bookingOption]),
                            $bookingOption->name,
                            formatInt($bookingOption->bookings_count ?? $bookingOption->bookings()->count())
                        )
                    ]);
                @endphp
                <div class="d-flex flex-wrap">
                    <x-bs::form.field name="booking_options" type="checkbox" :options="$checkBoxOptionForBookingOption" :allow-html="true"
                                      wire:model.live="bookingOptionIds"/>
                    @if(in_array($bookingOption->id, $bookingOptionIds, true))
                        @php
                            $showDetailsName = sprintf('show_details[%s]', $bookingOption->id);
                            $fields = $bookingOption->formFields->filter(fn (\App\Models\FormField $f) => !in_array($f->column, ['first_name', 'last_name', 'date_of_birth']));
                            $optionsForFields = \Portavice\Bladestrap\Support\Options::fromModels($fields, 'name');
                        @endphp
                        <x-bs::dropdown.button variant="light" class="ms-3 px-1 py-0">
                            <span class="small">{{ __('Show fields') }}</span>
                            <x-slot:dropdown>
                                @php
                                    $showDetailsName = sprintf('show_details[%s]', $bookingOption->id);
                                    $fields = $bookingOption->formFields->filter(
                                        fn (\App\Models\FormField $f) => $f->type->isFormField()
                                            && !in_array($f->column, ['first_name', 'last_name', 'date_of_birth'])
                                    );
                                    $optionsForFields = \Portavice\Bladestrap\Support\Options::fromModels($fields, 'name');
                                @endphp
                                <div class="mx-2">
                                    <x-bs::form.field :name="$showDetailsName" type="checkbox" :options="$optionsForFields"
                                                      wire:model.live="showFields"></x-bs::form.field>
                                </div>
                            </x-slot:dropdown>
                        </x-bs::dropdown.button>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="col-12 col-md-6 col-lg-3 col-xxl-2">
            @php
                $displayOptions = \Portavice\Bladestrap\Support\Options::fromArray([
                    'booked_at' => __('Booking date'),
                ]);
                if (\Illuminate\Support\Facades\Auth::user()?->can('viewAnyPaymentStatus', \App\Models\Booking::class)) {
                    $displayOptions->append(__('Payment status'), 'paid_at');
                }
                if (\Illuminate\Support\Facades\Auth::user()?->can('updateAnyBookingComment', \App\Models\Booking::class)) {
                    $displayOptions->append(__('Comments'), 'comment');
                }
            @endphp
            <x-bs::form.field name="show_booking_data" type="checkbox" :options="$displayOptions"
                              wire:model.live="showBookingData">{{ __('Display options') }}</x-bs::form.field>
        </div>
    </div>

    @include('layouts.alerts')

    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 col-xxl-3">
            @can('create', \App\Models\Group::class)
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="card-title">{{ __('Create group') }}</h2>
                    </div>
                    <form class="card-body" wire:submit.prevent="createGroup">
                        <x-bs::form.field name="form.name" type="text" maxlength="255" wire:model="form.name">{{ __('Name') }}</x-bs::form.field>
                        <x-bs::form.field name="form.description" type="textarea" maxlength="255" wire:model="form.description">{{ __('Description') }}</x-bs::form.field>
                        <x-bs::button><i class="fa fa-plus"></i> {{ __('Create') }}</x-bs::button>
                        <x-spinners.saving wire:target="createGroup"/>
                    </form>
                </div>
            @endcan
            <div class="mb-3"
                 x-on:dragover.prevent="allowDrop($event)"
                 x-on:drop.prevent="drop($event, -1)">
                <div class="card">
                    <div class="card-header bg-danger text-bg-danger">
                        <h2 class="card-title">{{ __('Without group') }} ({{ formatInt($bookingsWithoutGroup->count()) }})</h2>
                    </div>
                    <x-bs::list :flush="true" data-group-id="-1">
                        @include('livewire.groups.bookings', [
                            'bookings' => $bookingsWithoutGroup ?? \Illuminate\Database\Eloquent\Collection::empty(),
                            'groupId' => -1,
                        ])
                    </x-bs::list>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-8 col-xxl-9">
            <div class="row">
                @foreach($groups as $group)
                    @php
                        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
                        $bookings = $group['bookings'] ?? \Illuminate\Database\Eloquent\Collection::empty();
                        $editFormId = 'edit-form-' . $group->id;
                    @endphp
                    <div class="col-12 col-lg-6 col-xl-4 mb-3"
                         wire:key="{{ 'group' . $group->id }}"
                         x-on:dragover.prevent="allowDrop($event)"
                         x-on:drop.prevent="drop($event, {{ $group->id }})">
                        <div class="card">
                            <div class="card-header bg-secondary text-bg-secondary">
                                <h2 class="card-title">{{ $group->name }}</h2>
                                @isset($group->description)
                                    <p class="card-subtitle">{{ $group->description }}</p>
                                @endisset
                            </div>
                            @include('livewire.groups.bookings', [
                                'bookings' => $bookings,
                                'groupId' => $group->id,
                            ])
                            @canany(['update', 'forceDelete'], $group)
                                <div class="card-body">
                                    @can('update', $group)
                                        <x-bs::button.link data-bs-toggle="collapse" href="{{ '#' . $editFormId }}">
                                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                                        </x-bs::button.link>
                                    @endcan
                                    @can('forceDelete', $group)
                                        <x-bs::button type="button" variant="danger" x-on:click="showModal = true; groupIdToDelete = {{ $group->id }}; groupNameToDelete = '{{ $group->name }}';">
                                            <i class="fa fa-minus-circle"></i> {{ __('Delete') }}
                                        </x-bs::button>
                                    @endcan
                                </div>
                                @can('update', $group)
                                    <div id="{{ $editFormId }}" class="card-body collapse">
                                        <livewire:groups.edit-group :group="$group" wire:key="{{ 'edit-group' . $group->id }}"/>
                                    </div>
                                @endcan
                            @endcanany
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}" @click="showModal = false;"></button>
                </div>
                <div class="modal-body">{{ __('Are you sure you want to delete this group?') }}</div>
                <div class="modal-footer">
                    <x-bs::button type="button" data-bs-dismiss="modal" @click="showModal = false;"><i class="fa fa-window-close"></i> {{ __('Cancel') }}</x-bs::button>
                    <x-bs::button type="button" variant="danger" @click="$wire.deleteGroup(groupIdToDelete); showModal = false;">
                        <i class="fa fa-minus-circle"></i> {{ __('Delete') }}
                    </x-bs::button>
                    <x-spinners.deleting/>
                </div>
            </div>
        </div>
    </div>
</div>
