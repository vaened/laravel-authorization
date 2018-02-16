<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Exceptions\GrantableIsNotValidModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

abstract class Modifier
{
    protected function castToModel(Grantable $grantable): Model
    {
        if (! $grantable instanceof Model) {
            throw GrantableIsNotValidModelException::make($grantable);
        }

        return $grantable;
    }

    protected function resolveAuthorizationsRelation(Authorizable $user, Grantable $grantable): MorphToMany
    {
        if ($grantable instanceof PermissionContract) {
            return $user->permissions();
        }

        if ($grantable instanceof RoleContract) {
            return $user->roles();
        }
    }
}
