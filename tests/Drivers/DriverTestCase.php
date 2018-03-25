<?php

declare(strict_types=1);

/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Drivers;

use Enea\Authorization\Tests\TestCase;

abstract class DriverTestCase extends TestCase
{
    abstract protected function getDriver(): string;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app->make('config')->set('authorization.driver', $this->getDriver());
    }
}
