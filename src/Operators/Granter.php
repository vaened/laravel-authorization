<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Illuminate\Support\Collection;

class Granter extends Modifier
{
    public function grant(GrantableOwner $authorizationRepository, Grantable $grantable): void
    {
        $authorizations = $this->resolveAuthorizationRepository($authorizationRepository, $grantable);
        $granted = $authorizations->save($this->castToModel($grantable));

        if (is_null($granted)) {
            throw new AuthorizationNotGrantedException($grantable);
        }
    }

    public function syncGrant(GrantableOwner $authorizationRepository, Collection $grantableCollection): void
    {
        $grantableCollection->each($this->grantTo($authorizationRepository));
    }

    private function grantTo(GrantableOwner $authorizationRepository): Closure
    {
        return function (Grantable $grantable) use ($authorizationRepository): void {
            $this->grant($authorizationRepository, $grantable);
        };
    }
}
