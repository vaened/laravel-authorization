<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Revoker extends Modifier
{
    public function revoke(GrantableOwner $authorizationRepository, Grantable $grantable): void
    {
        $authorizations = $this->resolveAuthorizationRepository($authorizationRepository, $grantable);

        if (! $this->removeFrom($authorizations)($grantable)) {
            throw new AuthorizationNotGrantedException($grantable);
        }
    }

    public function syncRevoke(GrantableOwner $authorizationRepository, Collection $grantableCollection): void
    {
        $grantableCollection->each($this->revokeTo($authorizationRepository));
    }

    private function revokeTo(GrantableOwner $authorizationRepository): Closure
    {
        return function (Grantable $grantable) use ($authorizationRepository): void {
            $this->revoke($authorizationRepository, $grantable);
        };
    }

    private function removeFrom(BelongsToMany $authorizations): Closure
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
