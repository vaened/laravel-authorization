<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Middleware;

use Vaened\Authorization\Tests\Support\Models\User;
use Vaened\Authorization\Tests\TestCase;
use Illuminate\Routing\Router;

abstract class MiddlewareTestCase extends TestCase
{
    abstract protected function registerMiddleware(Router $router): void;

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
