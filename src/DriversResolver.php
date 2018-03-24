<?php
declare(strict_types=1);

/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Authorizer as AuthorizerContract;
use Enea\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;
use Enea\Authorization\Exceptions\UnsupportedDriverException;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Support\Drivers;
use Illuminate\Contracts\Container\Container;

class DriversResolver
{
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function make(): void
    {
        $this->resolve(Config::getDriver());
    }

    protected function resolve(string $driver): void
    {
        switch ($driver) {
            case Drivers::DATABASE:
                $this->prepareForDatabase();
                break;
            default:
                throw new UnsupportedDriverException($driver);
        }
    }

    private function prepareForDatabase(): void
    {
        $this->app->bind(AuthorizerContract::class, DatabaseAuthorizer::class);
    }
}
