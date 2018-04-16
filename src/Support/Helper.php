<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Illuminate\Support\Collection;

class Helper
{
    public function authenticated(?string $guard = null): ?Authorizable
    {
        $authenticated = auth($guard)->user();
        return $authenticated instanceof Authorizable ? $authenticated : null;
    }

    public function authorizer(): Authorizer
    {
        return app()->make(Authorizer::class);
    }

    public function except(Collection $grantableCollection, array $exceptNames): Collection
    {
        return $grantableCollection->filter(function (Grantable $grantable) use ($exceptNames): bool {
            return ! in_array($grantable, $exceptNames);
        });
    }
}
