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

class Granter extends Modifier
{
    public function grant(GrantableOwner $authorizationRepository, Grantable $grantable): void
    {
        $authorizations = $this->resolveAuthorizationRepository($authorizationRepository, $grantable);

        if (! $this->saveFor($authorizations)($grantable)) {
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

    protected function saveFor(BelongsToMany $authorizations): Closure
    {
        return function (Grantable $grantable) use ($authorizations): bool {
            return ! is_null($authorizations->save($this->castToModel($grantable)));
        };
    }
}
