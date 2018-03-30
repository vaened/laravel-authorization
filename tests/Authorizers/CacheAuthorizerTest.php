<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Authorizers;

use Enea\Authorization\Support\Drivers;

class CacheAuthorizerTest extends AuthorizerTestCase
{
    protected function getDriver(): string
    {
        return Drivers::CACHE;
    }
}
