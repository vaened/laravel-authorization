<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Support\Config;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        $this->syncGrant([$permission]);
    }

    public function syncGrant(array $permissions): void
    {
        Granter::permissions($this, collect($permissions));
    }

    public function revoke(PermissionContract $permission): void
    {
        $this->syncRevoke([$permission]);
    }

    public function syncRevoke(array $permissions): void
    {
        Revoker::permissions($this, collect($permissions));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Config::permissionModel(), Config::rolePermissionTableName(), 'role_id', 'permission_id');
    }

    public function getPermissionModels(): EloquentCollection
    {
        return $this->permissions;
    }
}
