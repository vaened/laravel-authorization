<?php
/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Middleware;

use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionsOwner;

class PermissionAuthorizerMiddleware extends AuthorizerMiddleware
{
    protected function authorized(GrantableOwner $owner, array $grantables): bool
    {
        if ($owner instanceof PermissionsOwner) {
            return $this->authorizer->canAny($owner, $grantables);
        }

        return false;
    }
}
