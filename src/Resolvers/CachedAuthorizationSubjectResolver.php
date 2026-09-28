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
use WeakMap;

final class CachedAuthorizationSubjectResolver implements AuthorizationSubjectResolver
{
    private WeakMap            $resolved;

    private bool               $guestResolved   = false;

    private ?SubjectResolution $guestResolution = null;

    public function __construct(private readonly AuthorizationSubjectResolver $resolver)
    {
        $this->resolved = new WeakMap();
    }

    public function resolve(object|null $user, Request $request): SubjectResolution
    {
        if (null === $user && $this->guestResolved) {
            return $this->guestResolution;
        }

        if (null !== $user && isset($this->resolved[$user])) {
            return $this->resolved[$user];
        }

        $resolution = $this->resolver->resolve($user, $request);

        if (!$resolution->isCacheable()) {
            return $resolution;
        }

        if (null === $user) {
            $this->guestResolution = $resolution;
            $this->guestResolved   = true;
        } else {
            $this->resolved[$user] = $resolution;
        }

        return $resolution;
    }
}
