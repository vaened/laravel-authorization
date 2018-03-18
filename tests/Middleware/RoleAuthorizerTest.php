<?php
/**
 * Created on 17/03/18 by enea dhack.
 */

namespace Enea\Authorization\Test\Middleware;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Middleware\RoleAuthorizerMiddleware;
use Illuminate\Routing\Router;

class RoleAuthorizerTest extends AuthorizerMiddlewareTestCase
{
    protected function getGrantableInstance(string $name): Grantable
    {
        return $this->role(['secret_name' => $name]);
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
