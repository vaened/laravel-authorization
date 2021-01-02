<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\AuthorizationServiceProvider;
use Enea\Authorization\Resolvers\DriverResolver;
use Enea\Authorization\Tests\Support\Models\User;
use Enea\Authorization\Tests\Support\Traits\Factories;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use Factories;

    public function setUp(): void
    {
        parent::setUp();
        $this->registerModelFactories();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthorizationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app->make('config');

        $config->set('auth.providers.users.model', User::class);

        $app->make('config')->set('view.paths', [__DIR__ . '/resources/views']);

        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function configDriver(string $driver)
    {
        $this->app->make('config')->set('authorization.driver', $driver);
        (new DriverResolver($this->app))->make();
    }

    protected function registerModelFactories(): void
    {
        $this->withFactories(__DIR__ . '/database/factories');
    }

    protected function setUpDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
        include_once __DIR__ . '/../database/migrations/create_laravel_authorization_tables.stub';
        (new \CreateLaravelAuthorizationTables())->up();
    }
}
