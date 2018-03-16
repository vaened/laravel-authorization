<?php
/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Middleware;

use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\RolesOwner;

class RoleAuthorizerMiddleware extends AuthorizerMiddleware
{
    protected function authorized(GrantableOwner $owner, array $grantables): bool
    {
        if ($owner instanceof RolesOwner) {
            return $this->authorizer->isAny($owner, $grantables);
        }

        return false;
    }
}
