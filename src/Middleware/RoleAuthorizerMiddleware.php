<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Middleware;

use Enea\Authorization\Contracts\Owner;
use Enea\Authorization\Contracts\RolesOwner;

class RoleAuthorizerMiddleware extends AuthorizerMiddleware
{
    protected function authorized(Owner $owner, array $grantables): bool
    {
        return $owner instanceof RolesOwner ? $this->authorizer->isAny($owner, $grantables) : false;
    }
}
