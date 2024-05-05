<div>
    @foreach($selectedUsers as $id => $selectedUser)
        <div class="row mb-3">
            <div class="col-12 col-xxl-4">
                <a href="{{ route('users.show', $selectedUser) }}">{{ $selectedUser->name }}</a>
                <x-bs::button type="button" variant="danger" class="btn-sm"
                              wire:click="removeUser({{ $id }})">
                    <i class="fa fa-fw fa-remove"></i> {{ __('Remove') }}
                </x-bs::button>
                <input type="hidden" name="responsible_user_id[]" value="{{ $id }}">
                <x-bs::form.field name="responsible_user_data[{{ $id }}][publicly_visible]"
                                  type="checkbox" :options="\Portavice\Bladestrap\Support\Options::one(__('publicly visible'))"
                                  :value="$selectedUser->pivot->publicly_visible ?? null"/>
            </div>
            <div class="col-12 col-lg-6 col-xxl-4">
                <x-bs::form.field name="responsible_user_data[{{ $id }}][position]"
                                  type="text" maxlength="255"
                                  :value="$selectedUser->pivot->position ?? null">{{ __('Position') }}</x-bs:x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xxl-4">
                <x-bs::form.field name="responsible_user_data[{{ $id }}][sort]"
                                  type="number" min="1" step="1" max="999999"
                                  :value="$selectedUser->pivot->sort ?? null">{{ __('Sort') }}</x-bs:x-bs::form.field>
            </div>
        </div>
    @endforeach

    <x-bs::form.field type="text" name="searchTerm"
                      placeholder="{{ __('Search users') }}"
                      wire:model.live.debounce.1000ms="searchTerm"/>
    @foreach($users as $user)
        <x-bs::button type="button" variant="secondary" class="btn-sm mb-1"
                      wire:click="addUser({{ $user->id }})">{{ __('Add :name', ['name' => $user->name]) }}</x-bs::button>
    @endforeach
    @if($users->count() < $usersCount)
        <strong>+ {{ formatTransChoice(':count more users', $usersCount - $users->count()) }}</strong>
    @endif
</div>
