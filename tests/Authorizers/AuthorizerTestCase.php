<?php
/**
 * Created on 11/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Authorizers;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Tests\Drivers\DriverTestCase;

abstract class AuthorizerTestCase extends DriverTestCase
{
    public function test_can_check_that_a_user_has_a_permission(): void
    {
        $user = $this->user();
        $user->grant($this->permission(['display_name' => 'See Articles']));
        $this->assertTrue($this->getAuthorizer()->can($user, 'see-articles'));
    }

    public function test_check_that_you_have_some_permissions(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3)->push($this->permission(['display_name' => 'Hide Articles']));
        $user->syncGrant($permissions->all());
        $this->assertTrue($this->getAuthorizer()->canAny($user, ['non-existent', 'hide-articles']));
    }

    public function test_can_check_that_you_have_a_nested_permission(): void
    {
        $user = $this->user();

        $role = $this->role();
        $role->grant($this->permission(['display_name' => 'Create Articles']));
        $role->grant($this->permission(['display_name' => 'Delete Articles']));

        $user->grant($role);
        $this->assertTrue($this->getAuthorizer()->can($user, 'create-articles'));
        $this->assertTrue($this->getAuthorizer()->can($user, 'delete-articles'));
    }

    public function test_can_check_that_a_user_has_a_role(): void
    {
        $user = $this->user();
        $user->grant($this->role(['display_name' => 'Articles Owner']));
        $this->assertTrue($this->getAuthorizer()->is($user, 'articles-owner'));
    }

    public function test_check_that_you_have_some_role(): void
    {
        $user = $this->user();
        $roles = $this->roles(3)->push($this->role(['display_name' => 'Admin']));
        $user->syncGrant($roles->all());
        $this->assertTrue($this->getAuthorizer()->isAny($user, ['non-existent', 'admin']));
    }

    protected function getAuthorizer(): Authorizer
    {
        return $this->app->make(Authorizer::class);
    }
}
