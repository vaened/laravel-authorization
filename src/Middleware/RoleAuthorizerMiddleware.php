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

use Vaened\Authorization\Support\Authenticated;

class RoleAuthorizerMiddleware extends AuthorizerMiddleware
{
    public function __construct(private readonly Authenticated $authenticated)
    {
    }

    protected function authorized(array $grantable): void
    {
        $this->authenticated->is(...$grantable);
    }
}
