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
use Illuminate\Http\Request;

abstract class AuthorizerMiddleware
{
    abstract protected function authorized(array $grantable): void;

    public function handle(Request $request, Closure $next, string ...$grantable)
    {
        $this->authorized($grantable);
        return $next($request);
    }
}
