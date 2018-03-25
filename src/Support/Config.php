<?php

declare(strict_types=1);

/**
 * Created on 20/02/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;

class Config
{
    public static function permissionModel(): string
    {
        return self::get('models.permission', Permission::class);
    }

    public static function roleModel(): string
    {
        return self::get('models.role', Role::class);
    }

    public static function userPermissionTableName(): string
    {
        return self::get('tables.user_permissions', 'user_permissions');
    }

    public static function userRoleTableName(): string
    {
        return self::get('tables.user_roles', 'user_roles');
    }

    public static function permissionTableName(): string
    {
        return self::get('tables.permission', 'permissions');
    }

    public static function roleTableName(): string
    {
        return self::get('tables.role', 'roles');
    }

    public static function rolePermissionTableName(): string
    {
        return self::get('tables.role_has_many_permissions', 'role_permissions');
    }

    public static function getDriver(): string
    {
        return self::get('driver', 'database');
    }

    private static function get(string $key, string $default = null): string
    {
        return config("authorization.{$key}", $default);
    }
}
