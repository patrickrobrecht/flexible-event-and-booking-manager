<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalAccessTokenRequest;
use App\Models\PersonalAccessToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PersonalAccessTokenController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewOwn', PersonalAccessToken::class);

        return view('personal_access_tokens.personal_access_token_index');
    }

    public function create(): View
    {
        $this->authorize('create', PersonalAccessToken::class);

        return view('personal_access_tokens.personal_access_token_form');
    }

    public function store(PersonalAccessTokenRequest $request): RedirectResponse
    {
        $this->authorize('create', PersonalAccessToken::class);

        $newAccessToken = PersonalAccessToken::createTokenFromValidated($request->user(), $request->validated());
        // phpcs:ignore Generic.Files.LineLength.TooLong
        Session::flash('success', __('Your personal access token is :token. Please make a note of it (e.g. in your password safe) - you will not be able to view it again.', [
            'token' => $newAccessToken->plainTextToken,
        ]));
        return redirect(route('personal-access-tokens.edit', $newAccessToken->accessToken));
    }

    public function edit(PersonalAccessToken $token): View
    {
        $this->authorize('update', $token);

        return view('personal_access_tokens.personal_access_token_form', [
            'token' => $token,
        ]);
    }

    public function update(PersonalAccessToken $token, PersonalAccessTokenRequest $request): RedirectResponse
    {
        $this->authorize('update', $token);

        if ($token->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
        }

        return back();
    }

    public function destroy(PersonalAccessToken $token): RedirectResponse
    {
        $this->authorize('forceDelete', $token);

        if ($token->forceDelete()) {
            Session::flash('success', __('Deleted successfully.'));
            return redirect(route('personal-access-tokens.index'));
        }

        return back();
    }
}
