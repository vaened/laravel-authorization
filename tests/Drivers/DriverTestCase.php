<?php

declare(strict_types=1);

/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Drivers;

use Enea\Authorization\Resolvers\DriverResolver;
use Enea\Authorization\Tests\TestCase;

abstract class DriverTestCase extends TestCase
{
    abstract protected function getDriver(): string;

    public function setUp()
    {
        parent::setUp();
        $this->app->make('config')->set('authorization.driver', $this->getDriver());
        (new DriverResolver($this->app))->make();
    }
}
