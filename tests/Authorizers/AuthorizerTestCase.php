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
        $user->grant($this->permission(['secret_name' => 'permission.1']));
        $this->assertTrue($this->getAuthorizer()->can($user, 'permission.1'));
    }

    public function test_check_that_you_have_some_permissions(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3)->push($this->permission(['secret_name' => 'permission.2']));
        $user->syncGrant($permissions->all());
        $this->assertTrue($this->getAuthorizer()->canAny($user, ['non-existent', 'permission.2']));
    }

    public function test_can_check_that_you_have_a_nested_permission(): void
    {
        $user = $this->user();

        $role = $this->role();
        $role->grant($this->permission(['secret_name' => 'permission.3']));
        $role->grant($this->permission(['secret_name' => 'permission.4']));

        $user->grant($role);
        $this->assertTrue($this->getAuthorizer()->can($user, 'permission.3'));
        $this->assertTrue($this->getAuthorizer()->can($user, 'permission.4'));
    }

    public function test_can_check_that_a_user_has_a_role(): void
    {
        $user = $this->user();
        $user->grant($this->role(['secret_name' => 'role.1']));
        $this->assertTrue($this->getAuthorizer()->is($user, 'role.1'));
    }

    public function test_check_that_you_have_some_role(): void
    {
        $user = $this->user();
        $roles = $this->roles(3)->push($this->role(['secret_name' => 'role.2']));
        $user->syncGrant($roles->all());
        $this->assertTrue($this->getAuthorizer()->isAny($user, ['non-existent', 'role.2']));
    }

    protected function getAuthorizer(): Authorizer
    {
        return $this->app->make(Authorizer::class);
    }
}
