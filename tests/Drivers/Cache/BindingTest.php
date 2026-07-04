<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Drivers\Cache;

use Vaened\Authorization\Authorizer;
use Vaened\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;

class BindingTest extends CacheTestCase
{
    public function test_get_the_cache_driver(): void
    {
        $this->assertInstanceOf(CacheAuthorizer::class, $this->app->make(Authorizer::class));
    }
}
