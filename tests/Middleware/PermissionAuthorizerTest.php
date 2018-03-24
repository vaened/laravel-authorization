<?php
/**
 * Created on 17/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Middleware;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Middleware\PermissionAuthorizerMiddleware;
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
        return $this->permission(['display_name' => $name]);
    }
}
