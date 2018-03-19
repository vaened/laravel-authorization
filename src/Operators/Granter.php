<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\{
    Grantable, GrantableOwner, PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Granted;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Granter extends Operator
{
    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $this->grantTo($owner->permissions())($permissions);
        $this->dispatchGrantedEvent($owner, $permissions);
    }

    public function roles(RolesOwner $owner, Collection $roles): void
    {
        $this->grantTo($owner->roles())($roles);
        $this->dispatchGrantedEvent($owner, $roles);
    }

    private function grantTo(BelongsToMany $repository): Closure
    {
        return function (Collection $grantableCollection) use ($repository): void {
            $grantableCollection->each($this->addTo($repository));
        };
    }

    protected function addTo(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): void {
            $saved = ! is_null($authorizations->save($this->castToModel($grantable)));

            if (! $saved) {
                throw new AuthorizationNotGrantedException($grantable);
            }
        };
    }

    private function dispatchGrantedEvent(GrantableOwner $owner, Collection $grantableCollection): void
    {
        $this->dispatchEvent(new Granted($owner, $grantableCollection));
    }
}
