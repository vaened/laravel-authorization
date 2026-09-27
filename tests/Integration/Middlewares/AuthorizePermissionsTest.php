<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Middlewares;

use Illuminate\Auth\Access\AuthorizationException;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Vaened\Authorization\Errors\InvalidAuthorizationSubject;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Middlewares\AuthorizePermissions;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Authorization\Tests\Runtime\TestSubject;

final class AuthorizePermissionsTest extends AuthorizeMiddlewareTestCase
{
    public function test_it_allows_the_request_when_the_user_has_the_required_permission(): void
    {
        $subject    = $this->subject();
        $permission = $this->permission('users.read', 'Read Users');

        $subject->grant($permission);

        $response = $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor($subject),
            static fn(): Response => new Response('ok'),
            'users.read',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_allows_a_non_eloquent_subject_with_the_required_permission(): void
    {
        $subject    = new TestSubject(1);
        $permission = $this->permission('users.read', 'Read Users');

        Granter::grant($subject, $permission);

        $response = $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor($subject),
            static fn(): Response => new Response('ok'),
            'users.read',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_uses_the_configured_subject_resolver(): void
    {
        $subject  = new TestSubject(99);
        $resolver = $this->createMock(AuthorizationSubjectResolver::class);

        $resolver->expects(self::once())
                 ->method('resolve')
                 ->willReturn($subject);

        $this->app->instance(AuthorizationSubjectResolver::class, $resolver);
        Granter::grant($subject, $this->permission('users.read', 'Read Users'));

        $response = $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor(new stdClass()),
            static fn(): Response => new Response('ok'),
            'users.read',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_throws_when_the_request_has_no_authenticated_user(): void
    {
        $this->expectException(AuthorizationException::class);

        $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor(),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }

    public function test_it_throws_when_the_user_is_not_authorizable(): void
    {
        $this->expectException(InvalidAuthorizationSubject::class);

        $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor(new stdClass()),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }

    public function test_it_throws_when_the_user_does_not_have_the_required_permission(): void
    {
        $this->expectException(AuthorizationException::class);

        $this->app->make(AuthorizePermissions::class)->handle(
            $this->requestFor($this->subject()),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }
}
