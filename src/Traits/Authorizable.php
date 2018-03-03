<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Support\Tables;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Trait Authorizable.
 *
 * @package Enea\Authorization\Traits
 *
 * @property EloquentCollection permissions
 * @property EloquentCollection roles
 */
trait Authorizable
{
    public function grant(Grantable $grantable): void
    {
        $this->syncGrant([$grantable]);
    }

    public function syncGrant(array $grantables): void
    {
        Granter::permissions($this, $this->filterPermissions($grantables));
        Granter::roles($this, $this->filterRoles($grantables));
    }

    public function revoke(Grantable $grantable): void
    {
        $this->syncRevoke([$grantable]);
    }

    public function syncRevoke(array $grantables): void
    {
        Revoker::permissions($this, $this->filterPermissions($grantables));
        Revoker::roles($this, $this->filterRoles($grantables));
    }

    public function permissions(): BelongsToMany
    {
        return $this->morphToMany(Tables::permissionModel(), 'authorizable', Tables::userPermissionModel());
    }

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(Tables::roleModel(), 'authorizable', Tables::userRoleModel());
    }

    public function getPermissionModels(): EloquentCollection
    {
        return $this->permissions;
    }

    public function getRoleModels(): EloquentCollection
    {
        return $this->roles;
    }

    private function filterPermissions(array $grantables): Collection
    {
        return collect($grantables)->filter(function (Grantable $grantable) {
            return $grantable instanceof PermissionContract;
        });
    }

    private function filterRoles(array $grantables): Collection
    {
        return collect($grantables)->filter(function (Grantable $grantable) {
            return $grantable instanceof RoleContract;
        });
    }
}
