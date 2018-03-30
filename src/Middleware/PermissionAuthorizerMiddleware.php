<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Middleware;

use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionsOwner;

class PermissionAuthorizerMiddleware extends AuthorizerMiddleware
{
    protected function authorized(GrantableOwner $owner, array $grantables): bool
    {
        return $owner instanceof PermissionsOwner ? $this->authorizer->canAny($owner, $grantables) : false;
    }
}
