<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;

class Tables
{
    public static function userPermissionModel(): string
    {
        return config('authorization.tables.user_permissions', 'user_permissions');
    }

    public static function userRoleModel(): string
    {
        return config('authorization.tables.user_roles', 'user_roles');
    }

    public static function permissionModel(): string
    {
        return config('authorization.models.permission', Permission::class);
    }

    public static function roleModel(): string
    {
        return config('authorization.models.role', Role::class);
    }

    public static function permissionName(): string
    {
        return config('authorization.tables.permission', 'permissions');
    }

    public static function roleName(): string
    {
        return config('authorization.tables.role', 'roles');
    }

    public static function rolePermissionName(): string
    {
        return config('authorization.tables.role_has_many_permissions', 'role_permissions');
    }
}
