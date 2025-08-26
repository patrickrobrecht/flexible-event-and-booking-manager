@can('forceDelete', $userRole)
    <x-form.delete-modal :id="$userRole->id"
                         :name="$userRole->name"
                         :route="route('user-roles.destroy', $userRole)"
                         :hint="__('By deleting the user role, the users with this role lose the abilities granted by this role.')"/>
@endcan
