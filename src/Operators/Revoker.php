<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

class Revoker extends Modifier
{
    public function revoke(Authorizable $user, Grantable $grantable): bool
    {
        $authorizations = $this->resolveAuthorizationsRelation($user, $grantable);
        return $this->removeFrom($authorizations)($grantable);
    }

    public function syncRevoke(Authorizable $user, Collection $grantableCollection): void
    {
        $grantableCollection->each($this->revokeTo($user));
    }

    private function revokeTo(Authorizable $user): Closure
    {
        return function (Grantable $grantable) use ($user) {
            return $this->revoke($user, $grantable);
        };
    }

    private function removeFrom(MorphToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): bool {
            return $this->isSuccessful($authorizations->detach($this->castToModel($grantable)));
        };
    }

    private function isSuccessful(int $results): bool
    {
        return $results > 0;
    }
}
