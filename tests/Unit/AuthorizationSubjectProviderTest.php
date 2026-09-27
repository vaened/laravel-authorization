<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Unit;

use Illuminate\Http\Request;
use stdClass;
use Vaened\Authorization\AuthorizationSubjectProvider;
use Vaened\Authorization\Errors\AuthorizationSubjectNotFound;
use Vaened\Authorization\Errors\InvalidAuthorizationSubject;
use Vaened\Authorization\Resolvers\AuthenticatedUserSubjectResolver;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Authorization\Tests\TestCase;

final class AuthorizationSubjectProviderTest extends TestCase
{
    public function test_the_default_resolver_returns_a_subject_user(): void
    {
        $subject = new TestSubject(1);
        $request = Request::create('/');
        $request->setUserResolver(static fn() => $subject);

        self::assertSame($subject, new AuthenticatedUserSubjectResolver()->resolve($subject, $request));
    }

    public function test_the_default_resolver_returns_null_for_an_unresolvable_user(): void
    {
        $request = Request::create('/');
        $request->setUserResolver(static fn() => null);

        self::assertNull(new AuthenticatedUserSubjectResolver()->resolve(null, $request));
    }

    public function test_the_default_resolver_rejects_a_user_that_is_not_a_subject(): void
    {
        $request = Request::create('/');

        $this->expectException(InvalidAuthorizationSubject::class);

        new AuthenticatedUserSubjectResolver()->resolve(new stdClass(), $request);
    }

    public function test_require_throws_a_package_exception_when_the_resolver_returns_null(): void
    {
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);
        $resolver->expects(self::once())->method('resolve')->willReturn(null);
        $provider = new AuthorizationSubjectProvider($resolver, Request::create('/'));

        $this->expectException(AuthorizationSubjectNotFound::class);

        $provider->current();
    }
}
