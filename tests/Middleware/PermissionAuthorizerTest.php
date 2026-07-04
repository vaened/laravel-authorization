<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Middleware;

use Vaened\Authorization\Contracts\Grantable;
use Vaened\Authorization\Middleware\PermissionAuthorizerMiddleware;
use Illuminate\Routing\Router;

class PermissionAuthorizerTest extends AuthorizerMiddlewareTestCase
{
    protected function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware($this->getMiddlewareName(), PermissionAuthorizerMiddleware::class);
    }

    protected function getMiddlewareName(): string
    {
        return 'authenticated.can';
    }

    protected function getGrantableInstance(string $name): Grantable
    {
        return $this->permission($name);
    }
}
