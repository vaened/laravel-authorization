<?php
/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Authorizer as AuthorizerContract;
use Enea\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;
use Enea\Authorization\Exceptions\UnsupportedDriverException;
use Enea\Authorization\Support\Config;
use Illuminate\Contracts\Foundation\Application;

class DriversResolver
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function make(): void
    {
        switch (Config::getDriver()) {
            case 'database':
                $this->prepareForDatabase();
                break;
            default:
                throw new UnsupportedDriverException(Config::getDriver());
        }
    }

    private function prepareForDatabase(): void
    {
        $this->app->bind(AuthorizerContract::class, DatabaseAuthorizer::class);
    }
}
