<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Events\Revoked;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Revoker extends Operator
{
    public function permission(PermissionsOwner $owner, PermissionContract $permission): void
    {
        $this->revokeTo($owner->permissions())($permission);
        $this->dispatchRevokedEvent($owner, $permission);
    }

    public function role(RolesOwner $owner, RoleContract $role): void
    {
        $this->revokeTo($owner->roles())($role);
        $this->dispatchRevokedEvent($owner, $role);
    }

    private function revokeTo(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): bool {
            $saved = $this->isSuccessful($authorizations->detach($this->castToModel($grantable)));
            $this->throwErrorIfNotSaved($saved, $grantable);
        };
    }

    private function isSuccessful(int $results): bool
    {
        return $results > 0;
    }

    private function dispatchRevokedEvent(GrantableOwner $owner, Grantable $permission): void
    {
        $this->dispatchEvent(Revoked::class, $owner, $permission);
    }
}
