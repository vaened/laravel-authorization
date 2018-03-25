<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Events\Revoked;
use Enea\Authorization\Exceptions\AuthorizationNotRevokedException;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Revoker;
use Illuminate\Support\Facades\Event;

class RevokeTest extends OperatorTestCase
{
    public function test_an_event_is_dispatched_when_permission_is_revoked()
    {
        Event::fake();
        $owner = $this->user();
        $permission = $this->permissions();
        $owner->syncGrant($permission->all());
        Revoker::permissions($owner, $permission);
        $this->assertEvent($owner, $permission, PermissionContract::class);
    }

    public function test_an_event_is_dispatched_when_role_is_revoked(): void
    {
        Event::fake();
        $owner = $this->user();
        $roles = $this->roles();
        $owner->syncGrant($roles->all());
        Revoker::roles($owner, $roles);
        $this->assertEvent($owner, $roles, RoleContract::class);
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

    protected function mainEventName(): string
    {
        return Revoked::class;
    }
}
