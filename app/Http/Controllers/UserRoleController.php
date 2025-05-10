<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\UserRoleFilterRequest;
use App\Http\Requests\UserRoleRequest;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class UserRoleController extends Controller
{
    public function index(UserRoleFilterRequest $request): View
    {
        $this->authorize('viewAny', UserRole::class);
        ValueHelper::setDefaults(UserRole::defaultValuesForQuery());

        return view('user_roles.user_role_index', [
            'userRoles' => UserRole::buildQueryFromRequest()
                ->withCount([
                    'users',
                ])
                ->paginate(18),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', UserRole::class);

        return view('user_roles.user_role_form');
    }

    public function store(UserRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', UserRole::class);

        $userRole = new UserRole();
        if ($userRole->fillAndSave($request->validated())) {
            Session::flash('success', __(':name created successfully.', ['name' => $userRole->name]));
            return $this->actionAwareRedirect($request, route('user-roles.index'), route('user-roles.create'));
        }

        return back();
    }

    public function show(UserRole $userRole): View
    {
        $this->authorize('view', $userRole);

        return view('user_roles.user_role_show', [
            'userRole' => $userRole,
        ]);
    }

    public function edit(UserRole $userRole): View
    {
        $this->authorize('update', $userRole);

        return view('user_roles.user_role_form', [
            'userRole' => $userRole,
        ]);
    }

    public function update(UserRole $userRole, UserRoleRequest $request): RedirectResponse
    {
        $this->authorize('update', $userRole);

        if ($userRole->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $userRole->name]));
            return redirect(route('user-roles.index'));
        }

        return back();
    }

    public function destroy(UserRole $userRole): RedirectResponse
    {
        $this->authorize('forceDelete', $userRole);

        if ($userRole->deleteAfterDetachingUsers()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $userRole->name]));
            return redirect(route('user-roles.index'));
        }

        return back();
    }
}
