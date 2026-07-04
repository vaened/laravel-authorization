<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Vaened\Authorization\Support\Config;
use Vaened\Authorization\Support\Determiner;
use Illuminate\Routing\Router;

class Middleware
{
    public function __construct(Router $router)
    {
        if (Determiner::isEnabledMiddleware()) {
            $this->enable($router);
        }
    }

    private function enable(Router $router): void
    {
        $router->aliasMiddleware(Config::getPermissionMiddlewareAlias(), Config::getPermissionMiddlewareClass());
        $router->aliasMiddleware(Config::getRoleMiddlewareAlias(), Config::getRoleMiddlewareClass());
    }
}
