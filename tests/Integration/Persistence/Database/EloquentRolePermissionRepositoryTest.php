<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Persistence\Database;

use Vaened\Authorization\Persistence\Database\EloquentRolePermissionRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;

final class EloquentRolePermissionRepositoryTest extends DatabaseTestCase
{
    private EloquentRolePermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentRolePermissionRepository();
    }

    public function test_lookup_returns_only_permissions_assigned_to_the_role_for_the_requested_codes(): void
    {
        $role       = $this->role('admin', 'Administrator');
        $permission = $this->permission('users.read', 'Read Users');
        $other      = $this->permission('users.update', 'Update Users');
        $foreign    = $this->permission('users.delete', 'Delete Users');
        $otherRole  = $this->role('editor', 'Editor');

        $this->repository->create($role, $permission, $other);
        $this->repository->create($otherRole, $foreign);

        $permissions = $this->repository->lookup($role, 'users.update', 'users.delete');

        self::assertCount(1, $permissions);
        self::assertSame(['users.update'], $permissions->codes());
    }

    public function test_exists_reports_when_a_permission_is_used_by_any_role(): void
    {
        $role       = $this->role('admin', 'Administrator');
        $permission = $this->permission('users.read', 'Read Users');

        $this->repository->create($role, $permission);

        self::assertTrue($this->repository->exists($permission->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_all_of_returns_every_permission_assigned_to_the_role(): void
    {
        $role        = $this->role('admin', 'Administrator');
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');

        $this->repository->create($role, $readUsers, $updateUsers);

        $permissions = $this->repository->allOf($role);

        self::assertCount(2, $permissions);
        self::assertSame(['users.read', 'users.update'], $permissions->codes());
    }

    public function test_create_persists_role_permission_bindings(): void
    {
        $role       = $this->role('admin', 'Administrator');
        $permission = $this->permission('users.read', 'Read Users');

        $this->repository->create($role, $permission);

        self::assertDatabaseHas('role_permissions', [
            'role_id'       => $role->id(),
            'permission_id' => $permission->id(),
        ]);
    }

    public function test_remove_deletes_only_the_requested_bindings(): void
    {
        $role        = $this->role('admin', 'Administrator');
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');

        $this->repository->create($role, $readUsers, $updateUsers);
        $this->repository->remove($role, $readUsers);

        self::assertDatabaseMissing('role_permissions', [
            'role_id'       => $role->id(),
            'permission_id' => $readUsers->id(),
        ]);

        self::assertDatabaseHas('role_permissions', [
            'role_id'       => $role->id(),
            'permission_id' => $updateUsers->id(),
        ]);
    }
}
