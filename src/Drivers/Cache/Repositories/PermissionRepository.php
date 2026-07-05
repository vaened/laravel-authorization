<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Cache\Repositories;

use Closure;
use Vaened\Authorization\Contracts\Deniable;
use Vaened\Authorization\Contracts\Grantable;
use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Vaened\Authorization\Support\Helper;
use Illuminate\Support\Collection;

class PermissionRepository extends Repository
{
    public static function getSuffix(): string
    {
        return 'permissions';
    }

    public function toCollection(PermissionsOwner $owner): Collection
    {
        return $this->remember($owner, function () use ($owner) {
            return $this->permissions($owner);
        });
    }

    private function permissions(PermissionsOwner $owner): Collection
    {
        $permissions = $owner->permissions()->get();

        if ($owner instanceof RolesOwner) {
            $names = $permissions->pluck('secret_name')->toArray();
            $permissions = $this->clean($this->permissionsFromRole($owner), $names)->merge($permissions);
        }

        return $permissions->filter($this->allowed())->map($this->parse());
    }

    private function allowed(): Closure
    {
        return function (PermissionContract $permission): bool {
            return $permission->pivot instanceof Deniable ? ! $permission->pivot->isDenied() : true;
        };
    }

    private function clean(Collection $permissions, array $exceptNames): Collection
    {
        return app(Helper::class)->except($permissions, $exceptNames);
    }

    private function permissionsFromRole(RolesOwner $owner): Collection
    {
        return $this->extractPermissions($owner)->unique(function (Grantable $grantable): string {
            return $grantable->getIdentificationKey();
        });
    }

    private function extractPermissions(RolesOwner $owner): Collection
    {
        return $owner->roles()->with('permissions')->get()->map(function (PermissionsOwner $owner): Collection {
            return $owner->getPermissionModels();
        })->collapse();
    }
}
