<?php
/**
 * Created on 22/02/18 by enea dhack.
 */

namespace Enea\Authorization\Drivers\Database;

use Closure;
use Enea\Authorization\Authorizer;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evaluator implements Authorizer
{
    public function can(PermissionsOwner $owner, string $permission): bool
    {
        return $this->has($owner->permissions())($permission);
    }

    public function is(RolesOwner $owner, string $role): bool
    {
        return $this->has($owner->roles())($role);
    }

    private function has(BelongsToMany $repository): Closure
    {
        return function (string $grantableName) use ($repository): bool {
            return $repository->where('secret_name', $grantableName)->exists();
        };
    }
}
