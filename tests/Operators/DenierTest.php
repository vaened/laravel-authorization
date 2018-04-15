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
use Enea\Authorization\Events\Denied;
use Enea\Authorization\Exceptions\AuthorizationNotDeniedException;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Denier;
use Enea\Authorization\Models\Permission;
use Illuminate\Support\Facades\Event;

class DenierTest extends OperatorTestCase
{
    public function test_an_event_is_dispatched_when_permission_is_denied(): void
    {
        Event::fake();
        $owner = $this->user();
        $permissions = $this->permissions();
        Denier::permissions($owner, $permissions);
        $this->assertEvent($owner, $permissions, PermissionContract::class);
    }

    public function test_when_an_permission_can_not_be_denied_an_exception_is_thrown(): void
    {
        $permission = new Permission(['secret_name' => 'secret']);
        $this->expectException(AuthorizationNotDeniedException::class);
        Denier::permissions($this->user(), collect([$permission]));
    }

    public function test_can_deny_permissions_to_a_owner(): void
    {
        $owner = $this->user();
        $create = $this->permission('Create Articles');
        $edit = $this->permission('Edit Articles');
        $permissions = collect([$create, $edit]);
        $owner->grantMultiple($permissions->all());
        $this->assertTrue($owner->can('edit-articles'));
        $this->assertTrue($owner->can('create-articles'));
        Denier::permissions($owner, $permissions);
        $this->assertFalse($owner->can('edit-articles'));
        $this->assertFalse($owner->can('create-articles'));
        $operations = $permissions->filter(function (PermissionContract $permission) use ($owner) {
            return ! Authorizer::can($owner, $permission->getSecretName());
        });
        $this->assertCount($permissions->count(), $operations);
    }

    protected function mainEventName(): string
    {
        return Denied::class;
    }
}
