<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers\Cache;

use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Drivers\Cache\Repositories\PermissionRepository;
use Enea\Authorization\Drivers\Cache\Repositories\RoleRepository;
use Enea\Authorization\Drivers\ManagerContract;
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

    public function forget(GrantableOwner $owner): void
    {
        if ($owner instanceof RolesOwner) {
            $this->roles->forget($owner);
        }

        if ($owner instanceof PermissionsOwner) {
            $this->permissions->forget($owner);
        }
    }
}
