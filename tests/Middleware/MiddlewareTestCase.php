<?php
/**
 * Created on 17/03/18 by enea dhack.
 */

namespace Enea\Authorization\Test\Middleware;

use Enea\Authorization\Test\Support\Models\User;
use Enea\Authorization\Test\TestCase;
use Illuminate\Routing\Router;

abstract class MiddlewareTestCase extends TestCase
{
    protected abstract function registerMiddleware(Router $router): void;

    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $this->registerMiddleware($app['router']);
    }

    protected function getRouter(): Router
    {
        return $this->app['router'];
    }

    protected function getLoggedUser(): User
    {
        $user = $this->user();
        $this->actingAs($user);
        return $user;
    }

    protected function authenticate(): void
    {
        $this->getLoggedUser();
    }
}
