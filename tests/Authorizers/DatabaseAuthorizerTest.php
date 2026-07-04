<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Authorizers;

use Vaened\Authorization\Support\Drivers;

class DatabaseAuthorizerTest extends AuthorizerTestCase
{
    protected function getDriver(): string
    {
        return Drivers::DATABASE;
    }
}
