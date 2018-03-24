<?php

declare(strict_types=1);

/**
 * Created on 18/02/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Events\Granted;
use Enea\Authorization\Exceptions\AuthorizationNotGrantedException;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class GranterTest extends TestCase
{
    public function test_an_event_is_dispatched_when_permission_is_granted(): void
    {
        Event::fake();
        $owner = $this->user();
        Granter::permissions($owner, $this->permissions());
        Event::assertDispatched(Granted::class);
    }

    public function test_when_an_role_can_not_be_granted_an_exception_is_thrown(): void
    {
        $role = new Role(['secret_name' => 'secret']);
        $this->expectException(AuthorizationNotGrantedException::class);
        Granter::roles($this->user(), collect([$role]));
    }

    public function test_when_an_permission_can_not_be_granted_an_exception_is_thrown()
    {
        $permission = new Permission(['secret_name' => 'secret']);
        $this->expectException(AuthorizationNotGrantedException::class);
        Granter::permissions($this->user(), collect([$permission]));
    }

    public function test_can_grant_permissions_to_a_owner(): void
    {
        $owner = $this->user();
        $permissions = $this->permissions(2);
        Granter::permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return Authorizer::can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }

    public function test_can_grant_roles_to_a_owner(): void
    {
        $owner = $this->user();
        $roles = $this->roles(2);
        Granter::roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return Authorizer::is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }
}
