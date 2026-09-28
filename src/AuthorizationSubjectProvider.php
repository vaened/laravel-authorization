<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Illuminate\Http\Request;
use Vaened\Authorization\Errors\AuthorizationSubjectNotFound;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Sentinel\Subject;

final readonly class AuthorizationSubjectProvider
{
    public function __construct(
        private AuthorizationSubjectResolver $resolver,
        private Request                      $request,
    )
    {
    }

    public function current(): Subject
    {
        $user = $this->request->user();
        $user = is_object($user) ? $user : null;

        $resolution = $this->resolver->resolve($user, $this->request);

        return $resolution->subject()
            ?? throw new AuthorizationSubjectNotFound();
    }
}
