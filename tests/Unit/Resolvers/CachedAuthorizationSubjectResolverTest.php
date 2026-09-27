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
                 ->willReturn($user);

        $cached = new CachedAuthorizationSubjectResolver($resolver);

        self::assertSame($user, $cached->resolve($user, $request));
        self::assertSame($user, $cached->resolve($user, $request));
    }

    public function test_it_caches_a_missing_subject(): void
    {
        $request  = Request::create('/');
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);

        $resolver->expects(self::once())
                 ->method('resolve')
                 ->with(null, $request)
                 ->willReturn(null);

        $cached = new CachedAuthorizationSubjectResolver($resolver);

        self::assertNull($cached->resolve(null, $request));
        self::assertNull($cached->resolve(null, $request));
    }
}
