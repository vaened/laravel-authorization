<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\AuthorizationWrapper;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Tables;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait Authorizable
 *
 * @package Enea\Authorization\Traits
 *
 * @property EloquentCollection permissions
 * @property EloquentCollection roles
 */
trait Authorizable
{
    public function grant(Grantable $grantable): bool
    {
        return Granter::grant($this, $grantable);
    }

    public function syncGrant(array $grantables): void
    {
        Granter::syncGrant($this, collect($grantables));
    }

    public function revoke(Grantable $grantable): bool
    {
        return Revoker::revoke($this, $grantable);
    }

    public function syncRevoke(array $grantables): void
    {
        Revoker::syncRevoke($this, collect($grantables));
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Tables::permissionModel(), 'authorizable', Tables::userPermissionModel());
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Tables::roleModel(), 'authorizable', Tables::userRoleModel());
    }

    public function getAuthorizationWrapper(): AuthorizationWrapper
    {
        return AuthorizationWrapper::fill($this->roles, $this->permissions);
    }
}
