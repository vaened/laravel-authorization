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

use Vaened\Authorization\Support\Drivers;
use Vaened\Authorization\Tests\Drivers\DriverTestCase;

class CacheTestCase extends DriverTestCase
{
    protected function getDriver(): string
    {
        return Drivers::CACHE;
    }
}
