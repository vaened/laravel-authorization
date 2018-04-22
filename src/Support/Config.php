<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Middleware\PermissionAuthorizerMiddleware;
use Enea\Authorization\Middleware\RoleAuthorizerMiddleware;
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
        return self::get('driver', Drivers::CACHE);
    }

    public static function getPermissionMiddlewareAlias(): string
    {
        return self::get('middleware.permissions.alias', 'authenticated.can');
    }

    public static function getPermissionMiddlewareClass(): string
    {
        return self::get('middleware.permissions.class', PermissionAuthorizerMiddleware::class);
    }

    public static function getRoleMiddlewareAlias(): string
    {
        return self::get('middleware.roles.alias', 'authenticated.is');
    }

    public static function getRoleMiddlewareClass(): string
    {
        return self::get('middleware.roles.class', RoleAuthorizerMiddleware::class);
    }

    private static function get(string $key, string $default = ''): string
    {
        return (string) config("authorization.{$key}", $default);
    }
}
