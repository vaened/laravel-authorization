<?php
/**
 * Created by enea dhack - 29/07/17 11:09 PM.
 */

namespace Enea\Authorization\Test;

use Enea\Authorization\AuthorizationServiceProvider;
use Enea\Authorization\Test\Support\Models\User;
use Enea\Authorization\Test\Support\Traits\Factories;
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

        $config->set('authorization', [
            'driver' => 'database',
            'tables' => [
                /// Table containing the roles.
                'role' => 'roles',
                /// Table containing the permissions.
                'permission' => 'permissions',
                /// Table containing the roles that belong to a role.
                'role_has_many_permissions' => 'role_permissions',
                /// Table that stores all roles per authorized user.
                'user_roles' => 'user_roles',
                /// Table that stores all permissions per authorized user.
                'user_permissions' => 'user_permissions',
            ],
        ]);
    }

    protected function registerModelFactories(): void
    {
        $this->withFactories(__DIR__ . '/Support/Factories');
    }

    protected function setUpDatabase(): void
    {
        include_once __DIR__ . '/../database/migrations/create_laravel_authorization_tables.php';
        include_once __DIR__ . '/Support/Migrations/laravel_authorization_test_tables.php';
        (new \CreateLaravelAuthorizationTables())->up();
        (new \CreateLaravelAuthorizationTestTables())->up();
    }
}
