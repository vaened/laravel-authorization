<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers\Cache\Listeners;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Drivers\Cache\Repositories\PermissionRepository;
use Enea\Authorization\Drivers\Cache\Repositories\RoleRepository;
use Enea\Authorization\Events\Operation;
use Illuminate\Support\Collection;

class OperatedOnAuthorization
{
    private $permissions;

    private $roles;

    public function __construct(PermissionRepository $permissions, RoleRepository $roles)
    {
        $this->permissions = $permissions;
        $this->roles = $roles;
    }

    public function handle(Operation $operation): void
    {
        $operated = $operation->getGrantableCollection();

        if ($this->contains(PermissionContract::class)($operated)) {
            $this->permissions->forget($operation->getOwner());
        }

        if ($this->contains(RoleContract::class)($operated)) {
            $this->roles->forget($operation->getOwner());
        }
    }

    private function contains(string $contract): Closure
    {
        return function (Collection $collection) use ($contract): bool {
            return $collection->contains(function (Grantable $grantable) use ($contract) {
                return $grantable instanceof $contract;
            });
        };
    }
}
