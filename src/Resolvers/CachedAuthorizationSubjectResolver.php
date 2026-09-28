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

final class CachedAuthorizationSubjectResolver implements AuthorizationSubjectResolver
{
    /** @var array<string, SubjectResolution> */
    private array $resolved = [];

    public function __construct(private readonly AuthorizationSubjectResolver $resolver)
    {
    }

    public function resolve(object|null $user, Request $request): SubjectResolution
    {
        $key = null === $user ? 'guest' : (string)spl_object_id($user);

        if (array_key_exists($key, $this->resolved)) {
            return $this->resolved[$key];
        }

        $resolution = $this->resolver->resolve($user, $request);

        if ($resolution->isCacheable()) {
            $this->resolved[$key] = $resolution;
        }

        return $resolution;
    }
}
