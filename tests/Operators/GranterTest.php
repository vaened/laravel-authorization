<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Operators;

use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Authorizer;
use Vaened\Authorization\Events\Granted;
use Vaened\Authorization\Exceptions\AuthorizationNotGrantedException;
use Vaened\Authorization\Models\Permission;
use Vaened\Authorization\Models\Role;
use Vaened\Authorization\Operators\Granter;
use Illuminate\Support\Facades\Event;

class GranterTest extends OperatorTestCase
{
    public function test_an_event_is_dispatched_when_permission_is_granted(): void
    {
        Event::fake();
        $owner = $this->user();
        $permissions = $this->permissions();
        $this->app->make(Granter::class)->permissions($owner, $permissions);
        $this->assertEvent($owner, $permissions, PermissionContract::class);
    }

    public function test_an_event_is_dispatched_when_role_is_granted(): void
    {
        Event::fake();
        $owner = $this->user();
        $roles = $this->roles();
        $this->app->make(Granter::class)->roles($owner, $roles);
        $this->assertEvent($owner, $roles, RoleContract::class);
    }

    public function test_when_an_role_can_not_be_granted_an_exception_is_thrown(): void
    {
        $role = new Role(['secret_name' => 'secret']);
        $this->expectException(AuthorizationNotGrantedException::class);
        $this->app->make(Granter::class)->roles($this->user(), collect([$role]));
    }

    public function test_when_an_permission_can_not_be_granted_an_exception_is_thrown()
    {
        $permission = new Permission(['secret_name' => 'secret']);
        $this->expectException(AuthorizationNotGrantedException::class);
        $this->app->make(Granter::class)->permissions($this->user(), collect([$permission]));
    }

    public function test_can_grant_permissions_to_a_owner(): void
    {
        $owner = $this->user();
        $permissions = $this->permissions(2);
        $this->app->make(Granter::class)->permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return $this->app->make(Authorizer::class)->can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }

    public function test_can_grant_roles_to_a_owner(): void
    {
        $owner = $this->user();
        $roles = $this->roles(2);
        $this->app->make(Granter::class)->roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return $this->app->make(Authorizer::class)->is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }

    protected function mainEventName(): string
    {
        return Granted::class;
    }
}
