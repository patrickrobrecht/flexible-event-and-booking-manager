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
            Session::flash('success', __('Created successfully.'));
            return redirect(route('user-roles.edit', $userRole));
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
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }
}
