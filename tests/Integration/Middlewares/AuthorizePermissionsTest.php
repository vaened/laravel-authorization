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
use Vaened\Authorization\Middlewares\AuthorizePermissions;

final class AuthorizePermissionsTest extends AuthorizeMiddlewareTestCase
{
    public function test_it_allows_the_request_when_the_user_has_the_required_permission(): void
    {
        $subject    = $this->subject();
        $permission = $this->permission('users.read', 'Read Users');

        $subject->grant($permission);

        $response = new AuthorizePermissions()->handle(
            $this->requestFor($subject),
            static fn(): Response => new Response('ok'),
            'users.read',
        );

        self::assertSame('ok', $response->getContent());
    }

    public function test_it_throws_when_the_request_has_no_authenticated_user(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizePermissions()->handle(
            $this->requestFor(),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }

    public function test_it_throws_when_the_user_is_not_authorizable(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizePermissions()->handle(
            $this->requestFor(new stdClass()),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }

    public function test_it_throws_when_the_user_does_not_have_the_required_permission(): void
    {
        $this->expectException(AuthorizationException::class);

        new AuthorizePermissions()->handle(
            $this->requestFor($this->subject()),
            static fn(): Response => new Response('ok'),
            'users.read',
        );
    }
}
