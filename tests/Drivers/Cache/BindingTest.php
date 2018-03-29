<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Drivers\Cache;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;

class BindingTest extends CacheTestCase
{
    public function test_get_the_cache_driver(): void
    {
        $this->assertInstanceOf(CacheAuthorizer::class, $this->app->make(Authorizer::class));
    }
}
