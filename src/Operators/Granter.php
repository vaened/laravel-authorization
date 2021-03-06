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
    Grantable, Owner, PermissionContract, PermissionsOwner, RolesOwner
};
use Enea\Authorization\Events\Granted;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Granter extends Operator
{
    use WithDeniablePermission;

    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $granted = $this->getPermissions($owner, $permissions);
        $granted->each($this->denialStatus($owner, false));
        $this->grantTo($owner->permissions())($this->except($granted, $permissions));
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
            $this->saveIn($grantable, $authorizations);
        };
    }

    private function saveIn(Grantable $grantable, BelongsToMany $authorizations): void
    {
        try {
            $authorizations->save($grantable);
        } catch (Exception $exception) {
            throw new AuthorizationNotGrantedException($grantable, $exception);
        }
    }

    private function dispatchGrantedEvent(Owner $owner, Collection $grantableCollection): void
    {
        $this->dispatchEvent(new Granted($owner, $grantableCollection));
    }

    protected function isModifiable(PermissionContract $permission): bool
    {
        return $permission->pivot->isDenied();
    }

    protected function throwException(Grantable $grantable): void
    {
        throw new AuthorizationNotGrantedException($grantable);
    }
}
