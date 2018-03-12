<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\{
    Grantable, GrantableOwner, PermissionContract, PermissionsOwner, RoleContract, RolesOwner
};
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

    abstract public function permission(PermissionsOwner $owner, PermissionContract $permission): void;

    abstract public function role(RolesOwner $owner, RoleContract $role): void;

    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $permissions->each(function (PermissionContract $permission) use ($owner) {
            $this->permission($owner, $permission);
        });
    }

    public function roles(RolesOwner $owner, Collection $roles): void
    {
        $roles->each(function (RoleContract $role) use ($owner) {
            $this->role($owner, $role);
        });
    }

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

    protected function dispatchEvent(string $event, GrantableOwner $owner, Grantable $grantable): void
    {
        $this->event->dispatch(new $event($owner, $grantable));
    }
}
