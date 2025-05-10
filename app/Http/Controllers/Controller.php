<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function actionAwareRedirect(
        FormRequest $request,
        string $indexRoute,
        ?string $createRoute = null,
        ?string $editRoute = null
    ): RedirectResponse {
        if (isset($createRoute) && $request->input('action') === 'create') {
            return redirect($createRoute);
        }

        if (isset($editRoute) && $request->input('action') === 'edit') {
            return redirect($editRoute);
        }

        return redirect($indexRoute);
    }
}
