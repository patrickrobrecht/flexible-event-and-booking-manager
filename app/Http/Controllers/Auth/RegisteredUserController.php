<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Options\ActiveStatus;
use App\Policies\UserPolicy;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $this->authorize('register', User::class); /** @see UserPolicy::register() */

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->authorize('register', User::class);

        $data = array_replace(
            ['status' => ActiveStatus::Active],
            $request->validated()
        );

        $user = new User();
        $user->fillAndSave($data);

        event(new Registered($user));

        Auth::login($user, $request->boolean('remember'));

        return redirect(RouteServiceProvider::HOME);
    }
}
