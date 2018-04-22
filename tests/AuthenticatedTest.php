<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Exceptions\InvalidModelException;
use Enea\Authorization\Exceptions\UnauthorizedOwnerException;
use Enea\Authorization\Facades\Authenticated;

class AuthenticatedTest extends TestCase
{
    public function test_checking_a_permission_with_an_unauthenticated_user_throws_an_exception(): void
    {
        $this->expectLogoutInvalidModelException();
        Authenticated::can('create');
    }

    public function test_checking_a_role_with_an_unauthenticated_user_throws_an_exception(): void
    {
        $this->expectLogoutInvalidModelException();
        Authenticated::is('admin');
    }

    public function test_consulting_a_permission_with_a_user_that_is_not_authoritative_throws_an_exception(): void
    {
        $this->expectLoginInvalidModelException();
        Authenticated::can('create');
    }

    public function test_consulting_a_role_with_a_user_that_is_not_authoritative_throws_an_exception(): void
    {
        $this->expectLoginInvalidModelException();
        Authenticated::is('admin');
    }

    public function test_an_exception_is_thrown_when_the_required_permission_is_not_obtained(): void
    {
        $this->expectUnauthorizedException();
        Authenticated::can('create');
    }

    public function test_an_exception_is_thrown_when_the_required_role_is_not_obtained(): void
    {
        $this->expectUnauthorizedException();
        Authenticated::is('admin');
    }

    private function expectLogoutInvalidModelException(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs(new TestUser());
        $this->expectException(InvalidModelException::class);
        Authenticated::can('create');
    }

    private function expectLoginInvalidModelException(): void
    {
        $this->withoutExceptionHandling();
        $this->user()->grant($this->permission('Create'));
        $this->expectException(InvalidModelException::class);
    }

    private function expectUnauthorizedException(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = $this->user());
        $this->expectException(UnauthorizedOwnerException::class);
        $this->expectExceptionMessage("{$user->getMorphClass()} with key {$user->getKey()} is not authorized");
    }
}

final class TestUser implements \Illuminate\Contracts\Auth\Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
}
