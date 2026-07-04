<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Drivers;

use Vaened\Authorization\Contracts\{
    PermissionContract, RoleContract
};
use Vaened\Authorization\Exceptions\UnsupportedDriverException;
use Vaened\Authorization\Resolvers\DriverResolver;
use Vaened\Authorization\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class BindingsTest extends TestCase
{
    public function test_throw_error_in_case_of_unsupported_driver(): void
    {
        $this->app->make('config')->set('authorization.driver', 'unsupported');
        $resolver = new DriverResolver($this->app);
        $this->expectException(UnsupportedDriverException::class);
        $resolver->make();
    }

    public function test_the_permission_contract_has_a_bound_model(): void
    {
        $this->assertInstanceOf(Model::class, $this->app->make(PermissionContract::class));
    }

    public function test_the_role_contract_has_a_bound_model(): void
    {
        $this->assertInstanceOf(Model::class, $this->app->make(RoleContract::class));
    }
}
