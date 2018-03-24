<?php

declare(strict_types=1);

/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Authorizers;

use Enea\Authorization\Support\Drivers;

class DatabaseAuthorizerTest extends AuthorizerTestCase
{
    protected function getDriver(): string
    {
        return Drivers::DATABASE;
    }
}
