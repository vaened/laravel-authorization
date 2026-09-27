<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;

final class AuthorizeRoles
{
    public function __construct(private readonly AuthorizationSubjectResolver $resolver)
    {
    }

    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user    = $request->user();
        $user    = is_object($user) ? $user : null;
        $subject = $this->resolver->resolve($user, $request);

        if (null === $subject || !Authorizer::is($subject, $roles)) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
