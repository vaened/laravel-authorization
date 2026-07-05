<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests;

use Vaened\Authorization\Exceptions\InvalidModelException;
use Vaened\Authorization\Exceptions\UnauthorizedOwnerException;
use Vaened\Authorization\Support\Authenticated;

class AuthenticatedTest extends TestCase
{
    public function test_checking_a_permission_with_an_unauthenticated_user_throws_an_exception(): void
    {
        $this->expectLogoutInvalidModelException();
        $this->app->make(Authenticated::class)->can('create');
    }

    public function test_checking_a_role_with_an_unauthenticated_user_throws_an_exception(): void
    {
        $this->expectLogoutInvalidModelException();
        $this->app->make(Authenticated::class)->is('admin');
    }

    public function test_consulting_a_permission_with_a_user_that_is_not_authoritative_throws_an_exception(): void
    {
        $this->expectLoginInvalidModelException();
        $this->app->make(Authenticated::class)->can('create');
    }

    public function test_consulting_a_role_with_a_user_that_is_not_authoritative_throws_an_exception(): void
    {
        $this->expectLoginInvalidModelException();
        $this->app->make(Authenticated::class)->is('admin');
    }

    public function test_an_exception_is_thrown_when_the_required_permission_is_not_obtained(): void
    {
        $this->expectUnauthorizedException();
        $this->app->make(Authenticated::class)->can('create');
    }

    public function test_an_exception_is_thrown_when_the_required_role_is_not_obtained(): void
    {
        $this->expectUnauthorizedException();
        $this->app->make(Authenticated::class)->is('admin');
    }

    private function expectLogoutInvalidModelException(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs(new TestUser());
        $this->expectException(InvalidModelException::class);
        $this->app->make(Authenticated::class)->can('create');
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
