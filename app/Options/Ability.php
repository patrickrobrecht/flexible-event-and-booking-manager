<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum Ability: string
{
    use NamedOption;

    case EditAccount = 'users.edit_account';
    case ManagePersonalAccessTokens = 'personal_access_tokens.manage_own';

    case ViewUsers = 'users.view';
    case CreateUsers = 'users.create';
    case EditUsers = 'users.edit';

    case ViewUserRoles = 'user_roles.view';
    case CreateUserRoles = 'user_roles.create';
    case EditUserRoles = 'user_roles.edit';

    public function getTranslatedName(): string
    {
        return match($this) {
            self::EditAccount => __('Edit own account'),
            self::ManagePersonalAccessTokens => __('Manage personal access tokens'),

            self::ViewUsers => __('View users'),
            self::CreateUsers => __('Create users'),
            self::EditUsers => __('Edit users'),

            self::ViewUserRoles => __('View user roles'),
            self::CreateUserRoles => __('Create user roles'),
            self::EditUserRoles => __('Edit user roles'),
        };
    }
}
