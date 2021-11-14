<?php

namespace App\Core\Security\Permission;


class UserRoles
{

    public const USER = "ROLE_USER";

    public const ADMIN = "ROLE_ADMIN";

    public const SUPER_ADMIN = "ROLE_SUPERADMIN";

    public const EDITOR = "ROLE_EDITOR";

    /**
     * return all roles as choice list
     */

    public static function getRoles(): array
    {

        return [
            "ADMIN" => Self::ADMIN,
            "SUPER_ADMIN" => Self::SUPER_ADMIN,
            "EDITOR" => Self::EDITOR
        ];
    }
}
