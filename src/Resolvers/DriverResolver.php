<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Resolvers;

use Enea\Authorization\Exceptions\UnsupportedDriverException;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Support\Drivers;
use Illuminate\Contracts\Container\Container;

class DriverResolver
{
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function make(): void
    {
        $this->getResolver(Config::getDriver())->configure();
    }

    private function getResolver(string $driver): Resolver
    {
        switch ($driver) {
            case Drivers::DATABASE:
                return new DatabaseDriverResolver($this->app);
            case Drivers::CACHE:
                return new CacheDriverResolver($this->app);
        }

        throw new UnsupportedDriverException($driver);
    }
}
