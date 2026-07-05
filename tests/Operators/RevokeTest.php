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
use Vaened\Authorization\Events\Revoked;
use Vaened\Authorization\Exceptions\AuthorizationNotRevokedException;
use Vaened\Authorization\Operators\Revoker;
use Illuminate\Support\Facades\Event;

class RevokeTest extends OperatorTestCase
{
    public function test_an_event_is_dispatched_when_permission_is_revoked()
    {
        Event::fake();
        $owner = $this->user();
        $permission = $this->permissions();
        $owner->grantMultiple($permission->all());
        $this->app->make(Revoker::class)->permissions($owner, $permission);
        $this->assertEvent($owner, $permission, PermissionContract::class);
    }

    public function test_an_event_is_dispatched_when_role_is_revoked(): void
    {
        Event::fake();
        $owner = $this->user();
        $roles = $this->roles();
        $owner->grantMultiple($roles->all());
        $this->app->make(Revoker::class)->roles($owner, $roles);
        $this->assertEvent($owner, $roles, RoleContract::class);
    }

    public function test_when_an_role_can_not_be_revoked_an_exception_is_thrown(): void
    {
        $user = $this->user();
        $this->expectException(AuthorizationNotRevokedException::class);
        $this->app->make(Revoker::class)->roles($user, $this->roles());
    }

    public function test_when_an_permission_can_not_be_revoked_an_exception_is_thrown(): void
    {
        $user = $this->user();
        $this->expectException(AuthorizationNotRevokedException::class);
        $this->app->make(Revoker::class)->permissions($user, $this->permissions());
    }

    public function test_can_revoke_permissions_to_a_owner(): void
    {
        $owner = $this->user();
        $permissions = $this->permissions(2);
        $owner->grantMultiple($permissions->all());
        $this->app->make(Revoker::class)->permissions($owner, $permissions);

        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return ! $this->app->make(Authorizer::class)->can($owner, $permission->getSecretName());
        });

        $this->assertSame($permissions->count(), $operations->count());
    }

    public function test_can_revoke_roles_to_a_owner(): void
    {
        $owner = $this->user();
        $roles = $this->roles(2);
        $owner->grantMultiple($roles->all());
        $this->app->make(Revoker::class)->roles($owner, $roles);

        $operations = $roles->filter(function (RoleContract $role) use ($owner) {
            return ! $this->app->make(Authorizer::class)->is($owner, $role->getSecretName());
        });

        $this->assertSame($roles->count(), $operations->count());
    }

    protected function mainEventName(): string
    {
        return Revoked::class;
    }
}
