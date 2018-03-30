<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Authorizers;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Tests\Drivers\DriverTestCase;

abstract class AuthorizerTestCase extends DriverTestCase
{
    public function test_can_check_that_a_user_has_a_permission(): void
    {
        $user = $this->user();
        $user->grant($this->permission('See Articles'));
        $this->assertTrue($this->getAuthorizer()->can($user, 'see-articles'));
    }

    public function test_can_check_that_a_user_does_not_have_a_permission(): void
    {
        $user = $this->user();
        $permission = $this->permission('See Articles');
        $user->grant($permission);
        $this->assertTrue($this->getAuthorizer()->can($user, 'see-articles'));
        $user->revoke($permission);
        $this->assertFalse($this->getAuthorizer()->can($user, 'see-articles'));
    }

    public function test_check_that_you_have_some_permissions(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3)->push($this->permission('Hide Articles'));
        $user->grantMultiple($permissions->all());
        $this->assertTrue($this->getAuthorizer()->canAny($user, ['non-existent', 'hide-articles']));
    }

    public function test_can_check_that_you_have_a_nested_permission(): void
    {
        $user = $this->user();

        $role = $this->role();
        $role->grant($this->permission('Create Articles'));
        $role->grant($this->permission('Delete Articles'));

        $user->grant($role);
        $this->assertTrue($this->getAuthorizer()->can($user, 'create-articles'));
        $this->assertTrue($this->getAuthorizer()->can($user, 'delete-articles'));
    }

    public function test_can_check_that_a_user_has_a_role(): void
    {
        $user = $this->user();
        $user->grant($this->role('Articles Owner'));
        $this->assertTrue($this->getAuthorizer()->is($user, 'articles-owner'));
    }

    public function test_can_check_that_a_user_does_not_have_a_role(): void
    {
        $user = $this->user();
        $role = $this->role('Articles Owner');
        $user->grant($role);
        $this->assertTrue($this->getAuthorizer()->is($user, 'articles-owner'));
        $user->revoke($role);
        $this->assertFalse($this->getAuthorizer()->is($user, 'articles-owner'));
    }

    public function test_check_that_you_have_some_role(): void
    {
        $user = $this->user();
        $roles = $this->roles(3)->push($this->role('Admin'));
        $user->grantMultiple($roles->all());
        $this->assertTrue($this->getAuthorizer()->isAny($user, ['non-existent', 'admin']));
    }

    protected function getAuthorizer(): Authorizer
    {
        return $this->app->make(Authorizer::class);
    }
}
