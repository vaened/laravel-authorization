<?php
/**
 * Created by enea dhack - 29/07/17 11:09 PM.
 */

namespace Enea\Authorization\Test;

use Enea\Authorization\AuthorizationServiceProvider;
use Enea\Authorization\Contracts\{
    PermissionContract, RoleContract
};
use Enea\Authorization\Support\Config;
use Enea\Authorization\Test\Support\Models\User;
use Enea\Authorization\Test\Support\Traits\Factories;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use Factories;

    public function setUp()
    {
        parent::setUp();
        $this->bindClasses();
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

        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function registerModelFactories(): void
    {
        $this->withFactories(__DIR__ . '/Support/Factories');
    }

    protected function bindClasses(): void
    {
        $this->app->bind(PermissionContract::class, Config::permissionModel());
        $this->app->bind(RoleContract::class, Config::roleModel());
    }

    protected function setUpDatabase(): void
    {
        include_once __DIR__ . '/../database/migrations/create_laravel_authorization_tables.php';
        include_once __DIR__ . '/Support/Migrations/laravel_authorization_test_tables.php';
        (new \CreateLaravelAuthorizationTables())->up();
        (new \CreateLaravelAuthorizationTestTables())->up();
    }
}
