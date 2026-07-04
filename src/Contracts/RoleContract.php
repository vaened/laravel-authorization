<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

interface RoleContract extends Grantable, Permissible, PermissionsOwner
{
    public static function locateByName(string $secretName): ?RoleContract;

    public function grant(PermissionContract $permission): void;

    public function grantMultiple(array $permissions): void;

    public function revoke(PermissionContract $permission): void;

    public function revokeMultiple(array $permissions): void;
}
