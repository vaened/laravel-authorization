<?php
/**
 * Created on 15/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests;

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

        $this->assertEquals(Config::getDriver(), 'database');
        $this->assertEquals(Config::roleModel(), Role::class);
        $this->assertEquals(Config::permissionModel(), Permission::class);
        $this->assertEquals(Config::roleTableName(), 'roles');
        $this->assertEquals(Config::permissionTableName(), 'permissions');
        $this->assertEquals(Config::rolePermissionTableName(), 'role_permissions');
        $this->assertEquals(Config::userPermissionTableName(), 'user_permissions');
        $this->assertEquals(Config::userRoleTableName(), 'user_roles');
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
        ]);
    }
}
