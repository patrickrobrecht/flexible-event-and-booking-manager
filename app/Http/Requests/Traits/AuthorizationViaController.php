<?php

namespace App\Http\Requests\Traits;

/**
 * Disables the authorization check in the request in favor of handling the check in the corresponding controller.
 */
trait AuthorizationViaController
{
    public function authorize(): bool
    {
        return true;
    }
}
