<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\{
    Grantable, PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Operation;
use Enea\Authorization\Exceptions\{
    AuthorizationNotGrantedException, GrantableIsNotValidModelException
};
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
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

    protected function castToModel(Grantable $grantable): Model
    {
        if (! $grantable instanceof Model) {
            throw GrantableIsNotValidModelException::make($grantable);
        }

        return $grantable;
    }

    protected function throwErrorIfNotSaved(bool $saved, Grantable $grantable): void
    {
        if (! $saved) {
            throw new AuthorizationNotGrantedException($grantable);
        }
    }

    protected function dispatchEvent(Operation $operation): void
    {
        $this->event->dispatch($operation);
    }
}
