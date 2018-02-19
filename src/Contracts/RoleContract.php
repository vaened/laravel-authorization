<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Support\Collection;

interface RoleContract extends Grantable, Permissible, PermissionsOwner
{
    public function grant(PermissionContract $permission): bool;

    public function revoke(PermissionContract $permission): bool;

    public function syncGrant(Collection $permissions): void;

    public function syncRevoke(Collection $permissions): void;
}
