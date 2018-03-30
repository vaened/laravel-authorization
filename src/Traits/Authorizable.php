<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        return (string) $this->getKey();
    }

    public function grant(Grantable $grantable): void
    {
        $this->grantMultiple([$grantable]);
    }

    public function grantMultiple(array $grantables): void
    {
        $this->operateOn(RoleContract::class, function (Collection $roles) {
            Granter::roles($this, $roles);
        }, $grantables);

        $this->operateOn(PermissionContract::class, function (Collection $permissions) {
            Granter::permissions($this, $permissions);
        }, $grantables);
    }

    public function revoke(Grantable $grantable): void
    {
        $this->revokeMultiple([$grantable]);
    }

    public function revokeMultiple(array $grantables): void
    {
        $this->operateOn(RoleContract::class, function (Collection $roles) {
            Revoker::roles($this, $roles);
        }, $grantables);

        $this->operateOn(PermissionContract::class, function (Collection $permissions) {
            Revoker::permissions($this, $permissions);
        }, $grantables);
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

    private function operateOn(string $contract, Closure $closure, array $grantables): void
    {
        $collection = $this->filterOnly($contract)($grantables);
        if (! $collection->isEmpty()) {
            $closure($collection);
        }
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
