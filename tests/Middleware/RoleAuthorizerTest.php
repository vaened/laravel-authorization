<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Middleware;

use Vaened\Authorization\Contracts\Grantable;
use Vaened\Authorization\Middleware\RoleAuthorizerMiddleware;
use Illuminate\Routing\Router;

class RoleAuthorizerTest extends AuthorizerMiddlewareTestCase
{
    protected function getGrantableInstance(string $name): Grantable
    {
        return $this->role($name);
    }

    protected function getMiddlewareName(): string
    {
        return 'authenticated.is';
    }

    protected function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware($this->getMiddlewareName(), RoleAuthorizerMiddleware::class);
    }
}
