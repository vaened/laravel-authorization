<?php
declare(strict_types=1);

/**
 * Created on 14/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Drivers\Database;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;

class BindingTest extends DatabaseTestCase
{
    public function test_get_the_database_driver(): void
    {
        $this->assertInstanceOf(DatabaseAuthorizer::class, $this->app->make(Authorizer::class));
    }
}
