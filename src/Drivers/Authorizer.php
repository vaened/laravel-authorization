<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers;

use Enea\Authorization\Authorizer as AuthorizerContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;

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
