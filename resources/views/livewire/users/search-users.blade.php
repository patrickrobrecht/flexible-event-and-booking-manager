<div>
    @foreach($selectedUsers as $id => $selectedUser)
        <input type="hidden" name="{{ $fieldName }}[]" value="{{ $id }}">
    @endforeach
    <div class="mb-3">
        @foreach($selectedUsers as $id => $selectedUser)
            <div class="btn btn-primary">
                <a class="text-white" href="{{ route('users.show', $selectedUser) }}">{{ $selectedUser->name }}</a>
                <small class="text-danger" wire:click="removeUser({{ $id }})"><i class="fa fa-fw fa-remove" title="{{ __('Remove') }}"></i></small>
            </div>
        @endforeach
    </div>

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
