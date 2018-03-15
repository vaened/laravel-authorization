<?php
/**
 * Created on 14/03/18 by enea dhack.
 */

namespace Enea\Authorization\Test\Drivers;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\DriversResolver;
use Enea\Authorization\Exceptions\UnsupportedDriverException;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Test\TestCase;

class BindingsTest extends TestCase
{
    public function test_throw_error_in_case_of_unsupported_driver(): void
    {
        $this->app->make('config')->set('authorization.driver', 'unsupported');
        $resolver = new DriversResolver($this->app);
        $this->expectException(UnsupportedDriverException::class);
        $resolver->make();
    }

    public function test_the_configured_permission_model_is_a_contract(): void
    {
        $this->assertInstanceOf(PermissionContract::class, $this->app->make(Config::permissionModel()));
    }

    public function test_the_configured_role_model_is_a_contract(): void
    {
        $this->assertInstanceOf(RoleContract::class, $this->app->make(Config::roleModel()));
    }
}
