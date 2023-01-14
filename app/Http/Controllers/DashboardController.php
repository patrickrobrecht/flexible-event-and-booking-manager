<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Options\Visibility;
use Carbon\Carbon;
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
            ->get();

        return view('dashboard.dashboard', [
            'events' => $events,
        ]);
    }
}
