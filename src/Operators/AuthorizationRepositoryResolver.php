<?php
/**
 * Created on 17/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Exceptions\NonAssignableGrantableModelException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuthorizationRepositoryResolver
{
    public function resolve(GrantableOwner $repository, Grantable $grantable): BelongsToMany
    {
        if ($this->needResolveForRole($grantable) && $repository instanceof RolesOwner) {
            return $repository->roles();
        }

        if ($this->needResolveForPermission($grantable) && $repository instanceof PermissionsOwner) {
            return $repository->permissions();
        }

        throw new NonAssignableGrantableModelException($repository, $grantable);
    }

    private function needResolveForRole(Grantable $grantable): bool
    {
        return $grantable instanceof RoleContract;
    }

    private function needResolveForPermission(Grantable $grantable): bool
    {
        return $grantable instanceof PermissionContract;
    }
}
