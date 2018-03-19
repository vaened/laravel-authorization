<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\{
    Grantable, GrantableOwner, PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Revoked;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Revoker extends Operator
{
    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $this->revokeTo($owner->permissions())($permissions);
        $this->dispatchRevokedEvent($owner, $permissions);
    }

    public function roles(RolesOwner $owner, Collection $roles): void
    {
        $this->revokeTo($owner->roles())($roles);
        $this->dispatchRevokedEvent($owner, $roles);
    }

    private function revokeTo(BelongsToMany $repository): Closure
    {
        return function (Collection $grantableCollection) use ($repository): void {
            $grantableCollection->each($this->removeFrom($repository));
        };
    }

    private function removeFrom(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): void {
            $saved = $this->isSuccessful($authorizations->detach($this->castToModel($grantable)));
            $this->throwErrorIfNotSaved($saved, $grantable);
        };
    }

    private function isSuccessful(int $results): bool
    {
        return $results > 0;
    }

    private function dispatchRevokedEvent(GrantableOwner $owner, Collection $grantableCollection): void
    {
        $this->dispatchEvent(new Revoked($owner, $grantableCollection));
    }
}
