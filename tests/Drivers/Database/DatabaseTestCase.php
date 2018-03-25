<?php

declare(strict_types=1);

/**
 * Created on 14/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Drivers\Database;

use Enea\Authorization\Support\Drivers;
use Enea\Authorization\Tests\Drivers\DriverTestCase;

class DatabaseTestCase extends DriverTestCase
{
    protected function getDriver(): string
    {
        return Drivers::DATABASE;
    }
}
