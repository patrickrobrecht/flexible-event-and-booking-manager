<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\UserFilterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Portavice\Bladestrap\Support\ValueHelper;

class UserController extends Controller
{
    public function index(UserFilterRequest $request): View
    {
        $this->authorize('viewAny', User::class);
        ValueHelper::setDefaults(User::defaultValuesForQuery());

        return view('users.user_index', [
            ...$this->formValues(),
            'users' => User::buildQueryFromRequest()
                ->with([
                    'userRoles',
                ])
                ->withCount([
                    'bookings',
                    'bookingsTrashed',
                    'documents',
                    'responsibleForEvents',
                    'responsibleForEventSeries',
                    'responsibleForOrganizations',
                    'tokens',
                ])
                ->paginate(12),
        ]);
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
            Session::flash('success', __(':name created successfully.', ['name' => $user->name]));
            if ($request->validated('send_notification') !== null) {
                $user->sendAccountCreatedNotification();
            }
            return $this->actionAwareRedirect(
                $request,
                route('users.show', $user),
                createRoute: route('users.create')
            );
        }

        return back();
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('users.user_show', [
            'user' => $user->loadProfileData(),
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.user_form', [
            ... $this->formValues(),
            'editedUser' => $user,
        ]);
    }

    public function update(User $user, UserRequest $request): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->fillAndSave($request->validated())) {
            Session::flash('success', __(':name saved successfully.', ['name' => $user->name]));
        }

        return $this->actionAwareRedirect(
            $request,
            route('users.show', $user),
            editRoute: route('users.edit', $user)
        );
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('forceDelete', $user);

        if ($user->deleteWithRelations()) {
            Session::flash('success', __(':name deleted successfully.', ['name' => $user->name]));
            return redirect(route('users.index'));
        }

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function formValues(): array
    {
        return [
            'userRoles' => UserRole::query()
                ->orderBy('name')
                ->get(),
        ];
    }
}
