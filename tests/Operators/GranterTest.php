<?php

declare(strict_types=1);

/**
 * Created on 18/02/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Operators;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Events\Granted;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Granter;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class GranterTest extends TestCase
{
    public function test_an_event_is_dispatched_when_permission_is_granted()
    {
        Event::fake();
        $owner = $this->user();
        Granter::permissions($owner, $this->permissions());
        Event::assertDispatched(Granted::class);
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
