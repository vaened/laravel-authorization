<?php
/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;

class RoleTest extends AuthorizationTestCase
{
    public function test_the_permissions_are_loaded_correctly(): void
    {
        $role = $this->role();
        $permissions = $this->permissions(3);
        $role->syncGrant($permissions->all());

        $role->getPermissionModels()->each(function (PermissionContract $permission) use ($permissions) {
            $this->assertTrue($permissions->contains($this->equalsAuthorization($permission)));
        });
    }

    public function test_a_role_can_have_permissions(): void
    {
        $role = $this->role();
        $permissions = $this->permissions(3);
        $role->syncGrant($permissions->all());

        $permissions->each(function (PermissionContract $permission) use ($role): void {
            $this->assertTrue($role->can($permission->getSecretName()));
        });
    }

    public function test_a_role_can_have_a_permission(): void
    {
        $role = $this->role();
        $permission = $this->permission();
        $role->grant($permission);
        $this->assertTrue($role->can($permission->getSecretName()));
    }

    public function test_you_can_remove_multiple_permissions_to_a_role(): void
    {
        $role = $this->role();
        $permissions = $this->permissions(3);
        $role->syncGrant($permissions->all());
        $role->syncRevoke($permissions->all());

        $permissions->filter(function (PermissionContract $permission) use ($role): void {
            $this->assertTrue($role->cannot($permission->getSecretName()));
        });
    }

    public function test_you_can_remove_single_permissions_to_a_role(): void
    {
        $role = $this->role();
        $permission = $this->permission();
        $role->grant($permission);
        $role->revoke($permission);
        $this->assertTrue($role->cannot($permission->getSecretName()));
    }

    protected function authorization(string $name): Grantable
    {
        return $this->role(['secret_name' => $name]);
    }

    private function equalsAuthorization(PermissionContract $permission): Closure
    {
        return function (PermissionContract $granted) use ($permission) : bool {
            return count(array_filter([
                    (string) $granted->getIdentificationKey() === $permission->getIdentificationKey(),
                    $granted->getSecretName() === $permission->getSecretName(),
                ])) === 2;
        };
    }
}
