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
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Middlewares\AuthorizeRoles;
use Vaened\Authorization\Tests\Runtime\TestSubject;

final class AuthorizeRolesTest extends AuthorizeMiddlewareTestCase
{
    public function test_it_allows_the_request_when_the_user_has_the_required_role(): void
    {
        $subject = $this->subject();
        $role    = $this->role('admin', 'Administrator');

        $subject->grant($role);

        $response = new AuthorizeRoles()->handle(
            $this->requestFor($subject),
            static fn(): Response => new Response('ok'),
            'admin',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_allows_a_non_eloquent_subject_with_the_required_role(): void
    {
        $subject = new TestSubject(1);
        $role    = $this->role('admin', 'Administrator');

        Granter::grant($subject, $role);

        $response = new AuthorizeRoles()->handle(
            $this->requestFor($subject),
            static fn(): Response => new Response('ok'),
            'admin',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_throws_when_the_request_has_no_authenticated_user(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizeRoles()->handle(
            $this->requestFor(),
            static fn(): Response => new Response('ok'),
            'admin',
        );
    }

    public function test_it_throws_when_the_user_is_not_authorizable(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizeRoles()->handle(
            $this->requestFor(new stdClass()),
            static fn(): Response => new Response('ok'),
            'admin',
        );
    }

    public function test_it_throws_when_the_user_does_not_have_the_required_role(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizeRoles()->handle(
            $this->requestFor($this->subject()),
            static fn(): Response => new Response('ok'),
            'admin',
        );
    }
}
