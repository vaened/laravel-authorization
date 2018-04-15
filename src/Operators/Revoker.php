<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\{
    Grantable, Owner, PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Revoked;
use Enea\Authorization\Exceptions\AuthorizationNotRevokedException;
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
            $result = $authorizations->detach($grantable->getIdentificationKey());

            if (! $this->isSuccessful($result)) {
                throw new AuthorizationNotRevokedException($grantable);
            }
        };
    }

    private function isSuccessful(int $results): bool
    {
        return $results > 0;
    }

    private function dispatchRevokedEvent(Owner $owner, Collection $grantableCollection): void
    {
        $this->dispatchEvent(new Revoked($owner, $grantableCollection));
    }
}
