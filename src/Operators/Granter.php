<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\{
    Grantable, GrantableOwner, PermissionContract, PermissionsOwner, RoleContract, RolesOwner
};
use Enea\Authorization\Events\Granted;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Granter extends Operator
{
    public function permission(PermissionsOwner $owner, PermissionContract $permission): void
    {
        $this->grantTo($owner->permissions())($permission);
        $this->dispatchGrantedEvent($owner, $permission);
    }

    public function role(RolesOwner $owner, RoleContract $role): void
    {
        $this->grantTo($owner->roles())($role);
        $this->dispatchGrantedEvent($owner, $role);
    }

    protected function grantTo(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): void {
            $saved = ! is_null($authorizations->save($this->castToModel($grantable)));
            $this->throwErrorIfNotSaved($saved, $grantable);
        };
    }

    private function dispatchGrantedEvent(GrantableOwner $owner, Grantable $permission): void
    {
        $this->dispatchEvent(new Granted($owner, $permission));
    }
}
