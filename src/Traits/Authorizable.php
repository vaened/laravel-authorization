<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Traits;

use Closure;
use Vaened\Authorization\Contracts\Grantable;
use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Authorization\Facades\Denier;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;
use Vaened\Authorization\Models\UserPermission;
use Vaened\Authorization\Support\Config;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Trait Authorizable.
 *
 * @package Vaened\Authorization\Traits
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property EloquentCollection permissions
 * @property EloquentCollection roles
 */
trait Authorizable
{
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

    public function deny(PermissionContract $permission): void
    {
        $this->denyMultiple([$permission]);
    }

    public function denyMultiple(array $permissions): void
    {
        Denier::permissions($this, collect($permissions));
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
        $params = [Config::permissionModel(), 'authorizable', Config::userPermissionTableName()];
        return $this->morphToMany(...$params)->using(UserPermission::class)->withPivot('denied');
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
