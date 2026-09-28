<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Unit\Resolvers;

use Illuminate\Http\Request;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Authorization\Resolvers\CachedAuthorizationSubjectResolver;
use Vaened\Authorization\Resolvers\SubjectResolution;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Authorization\Tests\TestCase;

final class CachedAuthorizationSubjectResolverTest extends TestCase
{
    public function test_it_resolves_each_user_once_per_scope(): void
    {
        $user     = new TestSubject(1);
        $request  = Request::create('/');
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);

        $resolver->expects(self::once())
                 ->method('resolve')
                 ->with($user, $request)
                 ->willReturn(SubjectResolution::found($user));

        $cached = new CachedAuthorizationSubjectResolver($resolver);

        self::assertSame($user, $cached->resolve($user, $request)->subject());
        self::assertSame($user, $cached->resolve($user, $request)->subject());
    }

    public function test_it_caches_a_missing_subject(): void
    {
        $request  = Request::create('/');
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);

        $resolver->expects(self::once())
                 ->method('resolve')
                 ->with(null, $request)
                 ->willReturn(SubjectResolution::notFound());

        $cached = new CachedAuthorizationSubjectResolver($resolver);

        self::assertTrue($cached->resolve(null, $request)->isNotFound());
        self::assertTrue($cached->resolve(null, $request)->isNotFound());
    }

    public function test_it_does_not_cache_an_unavailable_subject(): void
    {
        $user     = new TestSubject(1);
        $request  = Request::create('/');
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);

        $resolver->expects(self::exactly(2))
                 ->method('resolve')
                 ->with($user, $request)
                 ->willReturn(SubjectResolution::unavailable());

        $cached = new CachedAuthorizationSubjectResolver($resolver);

        self::assertTrue($cached->resolve($user, $request)->isUnavailable());
        self::assertTrue($cached->resolve($user, $request)->isUnavailable());
    }
}
