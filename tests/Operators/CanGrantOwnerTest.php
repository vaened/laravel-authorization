<?php
declare(strict_types=1);

/**
 * Created on 18/02/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Tests\TestCase;

class CanGrantOwnerTest extends TestCase
{
    public function test_can_grant_permissions_to_a_user(): void
    {
        $this->canOperateOnAPermitOwners($this->user());
    }

    public function test_can_grant_roles_to_a_user(): void
    {
        $this->canOperateOnARoleOwners($this->user());
    }

    public function test_can_grant_permissions_to_a_role(): void
    {
        $this->canOperateOnAPermitOwners($this->role());
    }

    protected function canOperateOnARoleOwners(RolesOwner $owner): void
    {
        $roles = $this->roles(2);
        Granter::roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return Authorizer::is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }

    protected function canOperateOnAPermitOwners(PermissionsOwner $owner): void
    {
        $permissions = $this->permissions(2);
        Granter::permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return Authorizer::can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }
}
