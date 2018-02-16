<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Illuminate\Support\Collection;

class Granter extends Modifier
{
    public function grant(Authorizable $user, Grantable $grantable): bool
    {
        $authorizations = $this->resolveAuthorizationsRelation($user, $grantable);
        return ! is_null($authorizations->save($this->castToModel($grantable)));
    }

    public function syncGrant(Authorizable $user, Collection $grantableCollection): void
    {
        $grantableCollection->each($this->grantTo($user));
    }

    private function grantTo(Authorizable $user): Closure
    {
        return function (Grantable $grantable) use ($user) {
            return $this->grant($user, $grantable);
        };
    }
}
