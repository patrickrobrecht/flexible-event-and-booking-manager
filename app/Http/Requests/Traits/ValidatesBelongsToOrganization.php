<?php

namespace App\Http\Requests\Traits;

use App\Models\Organization;

trait ValidatesBelongsToOrganization
{
    protected function getOrganizationFromRequest(): ?Organization
    {
        $organizationId = $this->input('organization_id');

        if (isset($organizationId) && is_numeric($organizationId)) {
            return Organization::query()->find($organizationId);
        }

        return null;
    }
}
