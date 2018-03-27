<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers\Cache\Repositories;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;

class PermissionRepository extends Repository
{
    protected function getSuffix(): string
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
        $permissions = $owner->getPermissionModels();

        if ($owner instanceof RolesOwner) {
            return $this->permissionsFromRole($owner)->merge($permissions)->map($this->parse());
        }

        return $permissions->map($this->parse());
    }

    private function permissionsFromRole(RolesOwner $owner): Collection
    {
        return $this->extractPermissions($owner)->unique(function (Grantable $grantable) {
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
