<?php

declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface RoleContract extends Grantable, Permissible, PermissionsOwner
{
    public static function locateByName(string $secretName): ?RoleContract;

    public function grant(PermissionContract $permission): void;

    public function revoke(PermissionContract $permission): void;

    public function syncGrant(array $permissions): void;

    public function syncRevoke(array $permissions): void;
}
