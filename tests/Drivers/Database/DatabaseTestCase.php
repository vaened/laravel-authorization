<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Drivers\Database;

use Vaened\Authorization\Support\Drivers;
use Vaened\Authorization\Tests\Drivers\DriverTestCase;

class DatabaseTestCase extends DriverTestCase
{
    protected function getDriver(): string
    {
        return Drivers::DATABASE;
    }
}
