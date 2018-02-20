<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Tables;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Trait HasRole.
 *
 * @package Enea\Authorization\Traits
 *
 * @property EloquentCollection permissions
 */
trait HasRole
{
    use Grantable, CanRefusePermission;

    public static function locateByName(string $secretName): ? RoleContract
    {
        return static::grantableBySecretName($secretName);
    }

    public function can(string $permission): bool
    {
        return true;
    }

    public function grant(PermissionContract $permission): void
    {
        Granter::grant($this, $permission);
    }

    public function syncGrant(Collection $permissions): void
    {
        Granter::syncGrant($this, $permissions);
    }

    public function revoke(PermissionContract $permission): void
    {
        Revoker::revoke($this, $permission);
    }

    public function syncRevoke(Collection $permissions): void
    {
        Revoker::syncRevoke($this, $permissions);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Tables::permissionModel(), Tables::rolePermissionName(), 'permission_id', 'role_id');
    }

    public function getPermissionModels(): EloquentCollection
    {
        return $this->permissions;
    }
}
