<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Middleware;

use Closure;
use Enea\Authorization\Authorizer;
use Enea\Authorization\Contracts\Owner;
use Enea\Authorization\Events\UnauthorizedOwner;
use Enea\Authorization\Exceptions\UnauthorizedOwnerException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

abstract class AuthorizerMiddleware
{
    protected $authorizer;

    private $event;

    public function __construct(Authorizer $authorizer, Dispatcher $event)
    {
        $this->authorizer = $authorizer;
        $this->event = $event;
    }

    abstract protected function authorized(Owner $owner, array $grantables): bool;

    public function handle(Request $request, Closure $next, string ...$grantables)
    {
        $authenticated = $request->user();

        if ($this->isAuthorizedRequestFor($authenticated)($grantables)) {
            return $next($request);
        }

        $this->event->dispatch(new UnauthorizedOwner($authenticated, $grantables));

        throw new UnauthorizedOwnerException($authenticated);
    }

    private function isAuthorizedRequestFor(Model $authenticated): Closure
    {
        return function (array $grantables) use ($authenticated): bool {
            return $authenticated instanceof Owner && $this->authorized($authenticated, $grantables);
        };
    }
}
