<?php
/**
 * Created on 21/02/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;

interface Authorizer
{
    public function can(PermissionsOwner $owner, string $permission): bool;

    public function cannot(PermissionsOwner $owner, string $permission): bool;

    public function memberOf(RolesOwner $owner, string $role): bool;

    public function notIsMemberOf(RolesOwner $owner, string $role): bool;
}
