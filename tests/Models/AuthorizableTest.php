<?php
/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Tests\TestCase;

class AuthorizableTest extends TestCase
{
    use PermissionOwnerTest, RoleOwnerTest, OwnerTest;

    public function test_the_permissions_are_loaded_correctly(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3);
        $user->syncGrant($permissions->all());

        $user->getPermissionModels()->each(function (PermissionContract $permission) use ($permissions) {
            $this->assertTrue($permissions->contains($this->equalsAuthorization($permission)));
        });
    }

    public function test_a_user_can_have_permissions(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3);
        $user->syncGrant($permissions->all());
        $permissions->each($this->can($user));
    }

    public function test_a_user_can_have_a_permission(): void
    {
        $user = $this->user();
        $permission = $this->permission();
        $user->grant($permission);
        $this->can($user)($permission);
    }

    public function test_you_can_remove_multiple_permissions_to_a_user(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(3);
        $user->syncGrant($permissions->all());
        $user->syncRevoke($permissions->all());
        $permissions->each($this->cannot($user));
    }

    public function test_you_can_remove_single_permissions_to_a_user(): void
    {
        $user = $this->user();
        $permission = $this->permission();
        $user->grant($permission);
        $user->revoke($permission);
        $this->cannot($user)($permission);
    }

    public function test_the_roles_are_loaded_correctly(): void
    {
        $user = $this->user();
        $roles = $this->roles(3);
        $user->syncGrant($roles->all());

        $user->getRoleModels()->each(function (RoleContract $role) use ($roles) {
            $this->assertTrue($roles->contains($this->equalsAuthorization($role)));
        });
    }

    public function test_a_user_can_have_roles(): void
    {
        $user = $this->user();
        $roles = $this->roles(3);
        $user->syncGrant($roles->all());
        $roles->each($this->is($user));
    }

    public function test_a_user_can_have_a_role(): void
    {
        $user = $this->user();
        $role = $this->role();
        $user->grant($role);
        $this->is($user)($role);
    }

    public function test_you_can_remove_multiple_roles_to_a_user(): void
    {
        $user = $this->user();
        $roles = $this->roles(3);
        $user->syncGrant($roles->all());
        $user->syncRevoke($roles->all());
        $roles->each($this->isnt($user));
    }

    public function test_you_can_remove_single_roles_to_a_user(): void
    {
        $user = $this->user();
        $role = $this->role();
        $user->grant($role);
        $user->revoke($role);
        $this->isnt($user)($role);
    }
}
