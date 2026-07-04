<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Cache;

use Vaened\Authorization\Contracts\Owner;
use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Vaened\Authorization\Drivers\Cache\Repositories\PermissionRepository;
use Vaened\Authorization\Drivers\Cache\Repositories\RoleRepository;
use Vaened\Authorization\Drivers\ManagerContract;
use Illuminate\Support\Collection;

class Manager implements ManagerContract
{
    private $permissions;

    private $roles;

    public function __construct(PermissionRepository $permissions, RoleRepository $roles)
    {
        $this->permissions = $permissions;
        $this->roles = $roles;
    }

    public function permissions(PermissionsOwner $owner): Collection
    {
        return $this->permissions->toCollection($owner);
    }

    public function roles(RolesOwner $owner): Collection
    {
        return $this->roles->toCollection($owner);
    }

    public function forget(Owner $owner): void
    {
        if ($owner instanceof RolesOwner) {
            $this->roles->forget($owner);
        }

        if ($owner instanceof PermissionsOwner) {
            $this->permissions->forget($owner);
        }
    }
}
