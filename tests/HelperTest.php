<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests;

use Vaened\Authorization\Contracts\Authorizable;
use Vaened\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;
use Vaened\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;
use Vaened\Authorization\Facades\Helper;
use Vaened\Authorization\Support\Drivers;

class HelperTest extends TestCase
{
    public function test_the_authorizer_method_returns_an_instance_of_the_configured_driver(): void
    {
        $this->configDriver(Drivers::CACHE);
        $this->assertInstanceOf(CacheAuthorizer::class, Helper::authorizer());

        $this->configDriver(Drivers::DATABASE);
        $this->assertInstanceOf(DatabaseAuthorizer::class, Helper::authorizer());
    }

    public function test_the_authenticated_method_returns_an_authenticated_user_instance(): void
    {
        $this->assertNull(Helper::authenticated());
        $user = $this->user();
        $this->actingAs($user);
        $this->assertInstanceOf(Authorizable::class, Helper::authenticated());
    }

    public function test_the_method_to_exclude_authorizations_works_correctly(): void
    {
        $edit = $this->permission('Edit');
        $create = $this->permission('Create');
        $delete = $this->permission('Delete');
        $permissions = collect([$create, $edit, $delete]);
        $allowed = Helper::except($permissions, [$edit->getSecretName()]);

        $this->assertCount(2, $allowed);
        $this->assertSame($allowed->first()->getSecretName(), $create->getSecretName());
        $this->assertSame($allowed->last()->getSecretName(), $delete->getSecretName());
    }
}
