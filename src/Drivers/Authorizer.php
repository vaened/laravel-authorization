<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers;

use Vaened\Authorization\Authorizer as AuthorizerContract;
use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;

abstract class Authorizer implements AuthorizerContract
{
    public function can(PermissionsOwner $owner, string $permission): bool
    {
        return $this->canAny($owner, [$permission]);
    }

    public function is(RolesOwner $owner, string $role): bool
    {
        return $this->isAny($owner, [$role]);
    }
}
