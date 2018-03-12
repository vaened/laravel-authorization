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
        return $this->syncEvaluate($owner, [$permission]);
    }

    public function syncEvaluate(PermissionsOwner $owner, array $permissions): bool
    {
        return $this->searchOnRoles($owner, $permissions) || $this->has($owner->permissions()->getQuery())($permissions);
    }

    private function searchOnRoles(PermissionsOwner $owner, array $permissions): bool
    {
        if ($owner instanceof RolesOwner) {
            return $owner->roles()->limit(1)->whereHas('permissions', $this->same($permissions))->exists();
        }

        return false;
    }
}
