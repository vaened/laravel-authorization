<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Drivers\Database;

use Vaened\Authorization\Authorizer;
use Vaened\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;

class BindingTest extends DatabaseTestCase
{
    public function test_get_the_database_driver(): void
    {
        $this->assertInstanceOf(DatabaseAuthorizer::class, $this->app->make(Authorizer::class));
    }
}
