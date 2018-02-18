<?php
/**
 * Created on 17/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionOwner;
use Enea\Authorization\Contracts\RoleAndPermissionOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Exceptions\NonAssignableGrantableModelException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AuthorizationRepositoryResolver
{
    public function resolve(GrantableOwner $repository, Grantable $grantable): BelongsToMany
    {
        if ($this->needResolveForRole($grantable)) {
            return $this->resolveForRole($repository, $grantable);
        }

        if ($repository instanceof PermissionOwner) {
            return $repository->permissions();
        }

        throw new NonAssignableGrantableModelException($repository, $grantable);
    }

    private function needResolveForRole(Grantable $grantable): bool
    {
        return $grantable instanceof RoleContract;
    }

    private function resolveForRole(GrantableOwner $repository, Grantable $grantable): BelongsToMany
    {
        if ($repository instanceof RoleAndPermissionOwner) {
            return $repository->roles();
        }

        throw new NonAssignableGrantableModelException($repository, $grantable);
    }
}
