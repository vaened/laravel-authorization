<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Enea\Authorization\Exceptions\GrantableIsNotValidModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Operator
{
    public abstract function permission(PermissionsOwner $owner, PermissionContract $permission): void;

    public abstract function role(RolesOwner $owner, RoleContract $role): void;

    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $permissions->each(function (PermissionContract $permission) use ($owner) {
            $this->permission($owner, $permission);
        });
    }

    public function roles(RolesOwner $owner, Collection $roles): void
    {
        $roles->each(function (RoleContract $role) use ($owner) {
            $this->role($owner, $role);
        });
    }

    protected function castToModel(Grantable $grantable): Model
    {
        if (! $grantable instanceof Model) {
            throw GrantableIsNotValidModelException::make($grantable);
        }

        return $grantable;
    }

    protected function throwErrorIfNotSaved(bool $saved, Grantable $grantable)
    {
        if (! $saved) {
            throw new AuthorizationNotGrantedException($grantable);
        }
    }
}
