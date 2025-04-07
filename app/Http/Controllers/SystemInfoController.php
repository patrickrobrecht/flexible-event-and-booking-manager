<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class SystemInfoController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewSystemInformation', User::class);

        return view('system.system-info');
    }
}
