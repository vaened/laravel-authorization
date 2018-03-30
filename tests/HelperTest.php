<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;
use Enea\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;
use Enea\Authorization\Facades\Helper;
use Enea\Authorization\Support\Drivers;

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
}
