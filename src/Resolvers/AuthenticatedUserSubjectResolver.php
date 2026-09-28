<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Resolvers;

use Illuminate\Http\Request;
use Vaened\Authorization\Errors\InvalidAuthorizationSubject;
use Vaened\Sentinel\Subject;

final class AuthenticatedUserSubjectResolver implements AuthorizationSubjectResolver
{
    public function resolve(object|null $user, Request $request): SubjectResolution
    {
        if (null === $user) {
            return SubjectResolution::notFound();
        }

        if (!$user instanceof Subject) {
            throw InvalidAuthorizationSubject::forUser($user);
        }

        return SubjectResolution::found($user);
    }
}
