@can('forceDelete', $user)
    @php
        $hint = null;
        if ($user->tokens_count) {
            $hint = formatTransChoice(':name has :count personal access tokens which will be deleted with the account.', $user->tokens_count, [
                'name' => $user->name,
            ]);
        }
    @endphp
    <x-form.delete-modal :id="$user->id"
                         :name="$user->name"
                         :hint="$hint"
                         :route="route('users.destroy', $user)"/>
@endcan
