<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Operators;

use Vaened\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};
use Vaened\Authorization\Events\Operation;
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
