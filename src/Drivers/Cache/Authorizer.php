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

use Closure;
use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Vaened\Authorization\Drivers\Authorizer as BaseAuthorizer;

class Authorizer extends BaseAuthorizer
{
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function canAny(PermissionsOwner $owner, array $permissions): bool
    {
        return $this->manager->permissions($owner)->contains($this->any($permissions));
    }

    public function isAny(RolesOwner $owner, array $roles): bool
    {
        return $this->manager->roles($owner)->contains($this->any($roles));
    }

    private function any(array $authorizations): Closure
    {
        return function (Struct $authorization) use ($authorizations): bool {
            return in_array($authorization->getName(), $authorizations);
        };
    }
}
