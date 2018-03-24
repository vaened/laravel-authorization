<?php
declare(strict_types=1);

/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Support\Collection;

class CanRevokeOwnerTest extends TestCase
{
    public function test_can_revoke_permissions_to_a_user(): void
    {
        $user = $this->user();
        $permissions = $this->permissions(2);
        $user->syncGrant($permissions->all());
        $this->canOperateOnAPermitOwners($user, $permissions);
    }

    public function test_can_revoke_roles_to_a_user(): void
    {
        $user = $this->user();
        $roles = $this->roles(2);
        $user->syncGrant($roles->all());
        $this->canOperateOnARoleOwners($user, $roles);
    }

    public function test_can_revoke_permissions_to_a_role(): void
    {
        $role = $this->role();
        $permissions = $this->permissions(2);
        $role->syncGrant($permissions->all());
        $this->canOperateOnAPermitOwners($role, $permissions);
    }

    protected function canOperateOnARoleOwners(RolesOwner $owner, Collection $roles): void
    {
        Revoker::roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return ! Authorizer::is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }

    protected function canOperateOnAPermitOwners(PermissionsOwner $owner, Collection $permissions): void
    {
        Revoker::permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return ! Authorizer::can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }
}
