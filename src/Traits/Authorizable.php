<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Support\Config;
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
    use Model;

    public function getIdentificationKey(): string
    {
        return $this->getKey();
    }

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

    public function can(string $permission): bool
    {
        return Authorizer::can($this, $permission);
    }

    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    public function isMemberOf(string $role): bool
    {
        return Authorizer::is($this, $role);
    }

    public function isntMemberOf(string $role): bool
    {
        return ! $this->isMemberOf($role);
    }

    public function permissions(): BelongsToMany
    {
        return $this->morphToMany(Config::permissionModel(), 'authorizable', Config::userPermissionTableName());
    }

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(Config::roleModel(), 'authorizable', Config::userRoleTableName());
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
        return $this->filterOnly(PermissionContract::class)($grantables);
    }

    private function filterRoles(array $grantables): Collection
    {
        return $this->filterOnly(RoleContract::class)($grantables);
    }

    private function filterOnly(string $abstract): Closure
    {
        return function (array $grantables) use ($abstract): Collection {
            return collect($grantables)->filter(function (Grantable $grantable) use ($abstract) {
                return $grantable instanceof $abstract;
            });
        };
    }
}
