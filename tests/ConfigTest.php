<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Middleware\PermissionAuthorizerMiddleware;
use Enea\Authorization\Middleware\RoleAuthorizerMiddleware;
use Enea\Authorization\Models\{
    Permission, Role
};
use Enea\Authorization\Support\Config;
use Enea\Authorization\Tests\Support\Models\{
    Permiso, Rol
};

class ConfigTest extends TestCase
{
    public function test_the_default_configuration_is_loaded(): void
    {
        $this->app->make('config')->set('authorization', null);

        $this->assertEquals(Config::getDriver(), 'cache');
        $this->assertEquals(Config::roleModel(), Role::class);
        $this->assertEquals(Config::permissionModel(), Permission::class);
        $this->assertEquals(Config::roleTableName(), 'roles');
        $this->assertEquals(Config::permissionTableName(), 'permissions');
        $this->assertEquals(Config::rolePermissionTableName(), 'role_permissions');
        $this->assertEquals(Config::userPermissionTableName(), 'user_permissions');
        $this->assertEquals(Config::userRoleTableName(), 'user_roles');
        $this->assertEquals(Config::getPermissionMiddlewareAlias(), 'authenticated.can');
        $this->assertEquals(Config::getPermissionMiddlewareClass(), PermissionAuthorizerMiddleware::class);
        $this->assertEquals(Config::getRoleMiddlewareAlias(), 'authenticated.is');
        $this->assertEquals(Config::getRoleMiddlewareClass(), RoleAuthorizerMiddleware::class);
    }

    public function test_custom_configuration_is_loaded(): void
    {
        $this->loadCustomConfig();
        $this->assertEquals(Config::getDriver(), 'another');
        $this->assertEquals(Config::roleModel(), Rol::class);
        $this->assertEquals(Config::permissionModel(), Permiso::class);
        $this->assertEquals(Config::roleTableName(), 'roles');
        $this->assertEquals(Config::permissionTableName(), 'permisos');
        $this->assertEquals(Config::rolePermissionTableName(), 'rol_permisos');
        $this->assertEquals(Config::userPermissionTableName(), 'usuario_permisos');
        $this->assertEquals(Config::userRoleTableName(), 'usuario_roles');
        $this->assertEquals(Config::getPermissionMiddlewareAlias(), 'can');
        $this->assertEquals(Config::getPermissionMiddlewareClass(), 'can-middleware');
        $this->assertEquals(Config::getRoleMiddlewareAlias(), 'is');
        $this->assertEquals(Config::getRoleMiddlewareClass(), 'is-middleware');
    }

    private function loadCustomConfig(): void
    {
        $this->app->make('config')->set('authorization', [
            'driver' => 'another',
            'models' => [
                'role' => Rol::class,
                'permission' => Permiso::class,
            ],
            'tables' => [
                'role' => 'roles',
                'permission' => 'permisos',
                'role_has_many_permissions' => 'rol_permisos',
                'user_permissions' => 'usuario_permisos',
                'user_roles' => 'usuario_roles',
            ],
            'middleware' => [
                'permissions' => [
                    'alias' => 'can',
                    'class' => 'can-middleware',
                ],
                'roles' => [
                    'alias' => 'is',
                    'class' => 'is-middleware',
                ],
            ]
        ]);
    }
}
