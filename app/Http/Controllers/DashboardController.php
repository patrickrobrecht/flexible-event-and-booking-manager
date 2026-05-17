<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->where('started_at', '>=', Carbon::now())
            ->where('visibility', '=', Visibility::Public->value)
            ->orderBy('started_at')
            ->limit(10)
            ->with([
                'location',
            ])
            ->withCount([
                'documents',
                'groups',
            ])
            ->get();

        /** @var ?User $user */
        $user = Auth::user();
        if (isset($user)) {
            $bookings = $user->bookings()
                ->orderByDesc('booked_at')
                ->limit(5)
                ->with([
                    'bookingOption.event.location',
                ])
                ->get();

            if ($user->can('viewAny', Document::class)) {
                $allDocumentsByStatus = Document::query()
                    ->selectRaw('count(*) as count, approval_status')
                    ->groupBy('approval_status')
                    ->visibleForUser() /** @see Document::scopeVisibleForUser() */
                    ->pluck('count', 'approval_status');
            }
        }

        return view('dashboard.dashboard', [
            'events' => $events,
            'bookings' => $bookings ?? null,
            'allDocumentsByStatus' => $allDocumentsByStatus ?? null,
        ]);
    }
}
