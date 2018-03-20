<?php
/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Tests\TestCase;

class AuthorizableTest extends TestCase
{
    use PermissionOwnerTest;

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
}
