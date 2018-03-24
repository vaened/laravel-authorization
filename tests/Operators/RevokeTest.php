<?php

declare(strict_types=1);

/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Events\Revoked;
use Enea\Authorization\Exceptions\AuthorizationNotRevokedException;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Revoker;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class RevokeTest extends TestCase
{
    public function test_an_event_is_dispatched_when_permission_is_granted()
    {
        Event::fake();
        $owner = $this->user();
        $permissions = $this->permissions();
        $owner->syncGrant($permissions->all());
        Revoker::permissions($owner, $permissions);
        Event::assertDispatched(Revoked::class);
    }

    public function test_when_an_role_can_not_be_revoked_an_exception_is_thrown(): void
    {
        $user = $this->user();
        $this->expectException(AuthorizationNotRevokedException::class);
        Revoker::roles($user, $this->roles());
    }

    public function test_when_an_permission_can_not_be_revoked_an_exception_is_thrown(): void
    {
        $user = $this->user();
        $this->expectException(AuthorizationNotRevokedException::class);
        Revoker::permissions($user, $this->permissions());
    }

    public function test_can_revoke_permissions_to_a_owner(): void
    {
        $owner = $this->user();
        $permissions = $this->permissions(2);
        $owner->syncGrant($permissions->all());
        Revoker::permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return ! Authorizer::can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }

    public function test_can_revoke_roles_to_a_owner(): void
    {
        $owner = $this->user();
        $roles = $this->roles(2);
        $owner->syncGrant($roles->all());
        Revoker::roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return ! Authorizer::is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }
}
