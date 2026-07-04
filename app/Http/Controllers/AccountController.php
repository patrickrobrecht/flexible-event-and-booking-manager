<?php

namespace App\Http\Controllers;

use App\Http\Requests\Filters\DocumentFilterRequest;
use App\Http\Requests\UserRequest;
use App\Models\Booking;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(): View
    {
        $this->authorize('viewAccount', User::class);

        /** @var User $user */
        $user = Auth::user();

        return view('account.account_show', [
            'user' => $user->loadProfileData(),
            ...$user->getMissingDocuments(),
        ]);
    }

    public function showAbilities(): View
    {
        $this->authorize('viewAccountAbilities', User::class);

        return view('account.account_show_abilities');
    }

    public function showBookings(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $bookings = $user->bookings()
            ->with([
                'bookingOption.event.location',
            ])
            ->paginate(12);
        $bookings->each(fn (Booking $booking) => $booking->setRelation('bookedByUser', $user));

        return view('account.account_show_bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function showDocuments(DocumentFilterRequest $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $documents = Document::buildQueryFromRequest($user->documents())
            ->with([
                'reference',
            ])
            ->withCount([
                'documentReviews',
            ])
            ->withMax('documentReviews', 'updated_at')
            ->paginate(12);
        $documents->each(fn (Document $document) => $document->setRelation('uploadedByUser', $user));

        return view('account.account_show_documents', [
            'documents' => $documents,
        ]);
    }

    public function edit(): View
    {
        $this->authorize('editAccount', User::class);

        return view('account.account_form');
    }

    public function update(UserRequest $request): RedirectResponse
    {
        $this->authorize('editAccount', User::class);

        /** @var User $user */
        $user = Auth::user();

        if ($user->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            return redirect(route('account.edit'));
        }

        return back();
    }
}
