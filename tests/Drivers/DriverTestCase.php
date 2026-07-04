<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Drivers;

use Vaened\Authorization\Tests\TestCase;

abstract class DriverTestCase extends TestCase
{
    abstract protected function getDriver(): string;

    public function setUp(): void
    {
        parent::setUp();
        $this->configDriver($this->getDriver());
    }
}
