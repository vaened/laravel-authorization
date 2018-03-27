<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Resolvers;

use Enea\Authorization\Authorizer as AuthorizerContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Event;

abstract class Resolver
{
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    abstract protected function authorizer(): string;

    public function configure()
    {
        $this->app->bind(AuthorizerContract::class, $this->authorizer());
        $this->configureEvents();
    }

    protected function listens(): array
    {
        return [];
    }

    protected function container(): Container
    {
        return $this->app;
    }

    private function configureEvents(): void
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
