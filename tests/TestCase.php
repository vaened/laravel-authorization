<?php

declare(strict_types=1);

/**
 * Created by enea dhack - 29/07/17 11:09 PM.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\AuthorizationServiceProvider;
use Enea\Authorization\Tests\Support\Models\User;
use Enea\Authorization\Tests\Support\Traits\Factories;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use Factories;

    public function setUp()
    {
        parent::setUp();
        $this->registerModelFactories();
        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthorizationServiceProvider::class,
            ConsoleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app->make('config');

        $config->set('auth.providers.users.model', User::class);

        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
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
