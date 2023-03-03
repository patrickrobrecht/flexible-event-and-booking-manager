<?php

namespace App\Http\Controllers;

use App\Helpers\QueryInput;
use App\Http\Requests\Filters\UserFilterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(UserFilterRequest $request): View
    {
        $this->authorize('viewAny', User::class);
        QueryInput::setDefaults(User::defaultValuesForFilters());

        return view('users.user_index', $this->formValues([
            'users' => User::filter()
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->with([
                    'userRoles',
                ])
                ->withCount([
                    'bookings',
                ])
                ->paginate(),
        ]));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.user_form', $this->formValues());
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = new User();
        if ($user->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('users.edit', $user));
        }

        return back();
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.user_form', $this->formValues([
            'editedUser' => $user,
        ]));
    }

    public function update(User $user, UserRequest $request): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'userRoles' => UserRole::query()
                ->orderBy('name')
                ->get(),
        ], $values);
    }
}
