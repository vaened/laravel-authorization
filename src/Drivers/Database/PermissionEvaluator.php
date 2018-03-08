<?php
/**
 * Created on 07/03/18 by enea dhack.
 */

namespace Enea\Authorization\Drivers\Database;

use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;

class PermissionEvaluator extends Evaluator
{
    public function evaluate(PermissionsOwner $owner, string $permission): bool
    {
        return $this->searchOnRoles($owner, $permission) || $this->has($owner->permissions()->getQuery())($permission);
    }

    private function searchOnRoles(PermissionsOwner $owner, string $permission): bool
    {
        if ($owner instanceof RolesOwner) {
            return $owner->roles()->limit(1)->whereHas('permissions', $this->equals($permission))->exists();
        }

        return false;
    }
}
