<?php

declare(strict_types=1);

namespace Vaened\Authorization\Configuration;

use InvalidArgumentException;

use function array_is_list;
use function array_key_exists;
use function count;
use function is_array;
use function is_string;
use function sprintf;
use function trim;

final class Synchronization
{
    public static function filename(): string
    {
        $key = config('authorization.synchronization.config', 'authorizations');

        if (!is_string($key) || trim($key) === '') {
            throw new InvalidArgumentException(
                'The authorization synchronization config key must be a non-empty string.',
            );
        }

        return $key;
    }

    public static function config(): string
    {
        $key           = self::filename();
        $configuration = config($key);

        if ($configuration === null) {
            throw new InvalidArgumentException(
                sprintf('The authorization synchronization config [%s] was not found.', $key),
            );
        }

        if (!is_array($configuration)) {
            throw new InvalidArgumentException(sprintf(
                "The authorization synchronization config [%s] must be an array with this structure:\n%s",
                $key,
                self::syntax(),
            ));
        }

        return $key;
    }

    /**
     * @return array<string, array<string, mixed>>|false
     */
    public static function permissions(): array|false
    {
        $permissions = config(self::config() . '.permissions', false);

        if ($permissions === null || $permissions === false) {
            return false;
        }

        self::validatePermissions($permissions);

        return $permissions;
    }

    /**
     * @return array<string, array<string, mixed>>|false
     */
    public static function roles(): array|false
    {
        $roles = config(self::config() . '.roles', false);

        if ($roles === null || $roles === false) {
            return false;
        }

        self::validateRoles($roles);

        return $roles;
    }

    /**
     * @param mixed $permissions
     */
    private static function validatePermissions(mixed $permissions): void
    {
        if (!is_array($permissions)) {
            self::invalid('permissions', self::permissionsSyntax());
        }

        foreach ($permissions as $code => $meta) {
            if (!is_string($code) || trim($code) === '' || !is_array($meta)) {
                self::invalid("permissions.$code", self::permissionsSyntax());
            }

            if (!array_key_exists('name', $meta) || !is_string($meta['name']) || trim($meta['name']) === '') {
                self::invalid("permissions.$code.name", self::permissionsSyntax());
            }

            if (array_key_exists('description', $meta)
                && $meta['description'] !== null
                && !is_string($meta['description'])) {
                self::invalid("permissions.$code.description", self::permissionsSyntax());
            }
        }
    }

    /**
     * @param mixed $roles
     */
    private static function validateRoles(mixed $roles): void
    {
        if (!is_array($roles)) {
            self::invalid('roles', self::rolesSyntax());
        }

        foreach ($roles as $code => $meta) {
            if (!is_string($code) || trim($code) === '' || !is_array($meta)) {
                self::invalid("roles.$code", self::rolesSyntax());
            }

            if (!array_key_exists('name', $meta) || !is_string($meta['name']) || trim($meta['name']) === '') {
                self::invalid("roles.$code.name", self::rolesSyntax());
            }

            if (array_key_exists('description', $meta)
                && $meta['description'] !== null
                && !is_string($meta['description'])) {
                self::invalid("roles.$code.description", self::rolesSyntax());
            }

            $rolePermissions = $meta['permissions'] ?? null;

            if (!is_array($rolePermissions) || !array_is_list($rolePermissions) || count($rolePermissions) === 0) {
                self::invalid("roles.$code.permissions", self::rolesSyntax());
            }

            foreach ($rolePermissions as $permissionCode) {
                if (!is_string($permissionCode) || trim($permissionCode) === '') {
                    self::invalid("roles.$code.permissions", self::rolesSyntax());
                }
            }
        }
    }

    private static function invalid(string $path, string $syntax): never
    {
        throw new InvalidArgumentException(sprintf(
            "Invalid authorization config at [%s]. Expected syntax:\n%s",
            $path,
            $syntax,
        ));
    }

    private static function syntax(): string
    {
        return self::permissionsSyntax() . "\n\n" . self::rolesSyntax();
    }

    private static function permissionsSyntax(): string
    {
        return <<<SYNTAX
'permissions' => [
    'permission.code' => [
        'name'        => 'Permission name',
        'description' => 'Optional description',
    ],
],
SYNTAX;
    }

    private static function rolesSyntax(): string
    {
        return <<<SYNTAX
'roles' => [
    'role.code' => [
        'name'        => 'Role name',
        'description' => 'Optional description',
        'permissions' => ['permission.code'],
    ],
],
SYNTAX;
    }
}
