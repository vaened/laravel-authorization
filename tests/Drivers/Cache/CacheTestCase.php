<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Drivers\Cache;

use Enea\Authorization\Support\Drivers;
use Enea\Authorization\Tests\Drivers\DriverTestCase;

class CacheTestCase extends DriverTestCase
{
    protected function getDriver(): string
    {
        return Drivers::CACHE;
    }
}
