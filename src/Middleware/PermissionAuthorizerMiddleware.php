<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Middleware;

use Vaened\Authorization\Facades\Authenticated;

class PermissionAuthorizerMiddleware extends AuthorizerMiddleware
{
    protected function authorized(array $grantable): void
    {
        Authenticated::can(...$grantable);
    }
}
