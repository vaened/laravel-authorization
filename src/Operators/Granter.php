<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Granter extends Operator
{
    public function permission(PermissionsOwner $owner, PermissionContract $permission): void
    {
        $this->grantTo($owner->permissions())($permission);
    }

    public function role(RolesOwner $owner, RoleContract $role): void
    {
        $this->grantTo($owner->roles())($role);
    }

    protected function grantTo(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): void {
            $saved = ! is_null($authorizations->save($this->castToModel($grantable)));
            $this->throwErrorIfNotSaved($saved, $grantable);
        };
    }
}
