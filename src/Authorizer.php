<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization;

use Vaened\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};

interface Authorizer
{
    public function can(PermissionsOwner $owner, string $permission): bool;

    public function canAny(PermissionsOwner $owner, array $permissions): bool;

    public function is(RolesOwner $owner, string $role): bool;

    public function isAny(RolesOwner $owner, array $roles): bool;
}
