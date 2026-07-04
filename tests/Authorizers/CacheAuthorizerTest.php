<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Authorizers;

use Vaened\Authorization\Support\Drivers;

class CacheAuthorizerTest extends AuthorizerTestCase
{
    protected function getDriver(): string
    {
        return Drivers::CACHE;
    }
}
