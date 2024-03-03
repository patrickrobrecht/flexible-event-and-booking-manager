<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupController extends Controller
{
    public function index(
        Event $event,
    ): StreamedResponse|View {
        $this->authorize('viewAny', [Group::class, $event]);

        return view('groups.group_index', [
            'event' => $event,
        ]);
    }
}
