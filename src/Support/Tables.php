<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;

class Tables
{
    public static function userPermissionModel(): string
    {
        return Config::get('tables.user_permissions', 'user_permissions');;
    }

    public static function userRoleModel(): string
    {
        return Config::get('tables.user_roles', 'user_roles');
    }

    public static function permissionModel(): string
    {
        return Config::get('models.permission', Permission::class);
    }

    public static function roleModel(): string
    {
        return Config::get('models.role', Role::class);
    }

    public static function permissionName(): string
    {
        return Config::get('tables.permission', 'permissions');
    }

    public static function roleName(): string
    {
        return Config::get('tables.role', 'roles');
    }

    public static function rolePermissionName(): string
    {
        return Config::get('tables.role_has_many_permissions', 'role_permissions');
    }
}
