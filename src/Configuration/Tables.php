<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Configuration;

final class Tables
{
    public static function roles(string|null $column = null): string
    {
        return self::qualify('roles', $column);
    }

    public static function permissions(string|null $column = null): string
    {
        return self::qualify('permissions', $column);
    }

    public static function rolePermissions(string|null $column = null): string
    {
        return self::qualify('role_permissions', $column);
    }

    public static function subjectRoles(string|null $column = null): string
    {
        return self::qualify('subject_roles', $column);
    }

    public static function subjectPermissions(string|null $column = null): string
    {
        return self::qualify('subject_permissions', $column);
    }

    protected static function qualify(string $key, string|null $column = null): string
    {
        $table = config("authorization.tables.$key");

        return null === $column ? $table : "$table.$column";
    }
}
