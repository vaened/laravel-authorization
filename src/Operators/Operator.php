<?php

declare(strict_types=1);

/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Operation;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;

abstract class Operator
{
    private $event;

    public function __construct(Dispatcher $event)
    {
        $this->event = $event;
    }

    abstract public function permissions(PermissionsOwner $owner, Collection $permissions): void;

    abstract public function roles(RolesOwner $owner, Collection $roles): void;

    protected function dispatchEvent(Operation $operation): void
    {
        $this->event->dispatch($operation);
    }
}
