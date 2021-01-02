<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Drivers;

use Enea\Authorization\Tests\TestCase;

abstract class DriverTestCase extends TestCase
{
    abstract protected function getDriver(): string;

    public function setUp(): void
    {
        parent::setUp();
        $this->configDriver($this->getDriver());
    }
}
