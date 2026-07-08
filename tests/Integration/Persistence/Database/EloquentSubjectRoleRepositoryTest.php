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
use Vaened\Authorization\Persistence\Database\EloquentSubjectRoleRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;

final class EloquentSubjectRoleRepositoryTest extends DatabaseTestCase
{
    private EloquentSubjectRoleRepository $repository;
    private EloquentRolePermissionRepository $rolePermissions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository      = new EloquentSubjectRoleRepository();
        $this->rolePermissions = new EloquentRolePermissionRepository();
    }

    public function test_lookup_returns_only_roles_assigned_to_the_subject_for_the_requested_codes(): void
    {
        $subject  = $this->subject();
        $admin    = $this->role('admin', 'Administrator');
        $editor   = $this->role('editor', 'Editor');
        $auditor  = $this->role('auditor', 'Auditor');
        $stranger = $this->subject();

        $this->repository->create($subject, $admin, $editor);
        $this->repository->create($stranger, $auditor);

        $roles = $this->repository->lookup($subject, 'editor', 'auditor');

        self::assertCount(1, $roles);
        self::assertSame(['editor'], $roles->codes());
    }

    public function test_grants_returns_distinct_permissions_inherited_from_the_subject_roles(): void
    {
        $subject      = $this->subject();
        $admin        = $this->role('admin', 'Administrator');
        $editor       = $this->role('editor', 'Editor');
        $readUsers    = $this->permission('users.read', 'Read Users');
        $updateUsers  = $this->permission('users.update', 'Update Users');
        $deleteUsers  = $this->permission('users.delete', 'Delete Users');

        $this->rolePermissions->create($admin, $readUsers, $updateUsers);
        $this->rolePermissions->create($editor, $updateUsers, $deleteUsers);
        $this->repository->create($subject, $admin, $editor);

        $permissions = $this->repository->grants($subject, 'users.read', 'users.update', 'users.delete');

        self::assertCount(3, $permissions);
        self::assertEqualsCanonicalizing(['users.read', 'users.update', 'users.delete'], $permissions->codes());
    }

    public function test_exists_reports_when_a_role_is_assigned_to_any_subject(): void
    {
        $subject = $this->subject();
        $role    = $this->role('admin', 'Administrator');

        $this->repository->create($subject, $role);

        self::assertTrue($this->repository->exists($role->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_all_of_returns_every_role_assigned_to_the_subject(): void
    {
        $subject = $this->subject();
        $admin   = $this->role('admin', 'Administrator');
        $editor  = $this->role('editor', 'Editor');

        $this->repository->create($subject, $admin, $editor);

        $roles = $this->repository->allOf($subject);

        self::assertCount(2, $roles);
        self::assertSame(['admin', 'editor'], $roles->codes());
    }

    public function test_create_persists_subject_role_bindings_with_morph_data(): void
    {
        $subject = $this->subject();
        $role    = $this->role('admin', 'Administrator');

        $this->repository->create($subject, $role);

        self::assertDatabaseHas('subject_roles', [
            'role_id'           => $role->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);
    }

    public function test_remove_deletes_only_the_requested_subject_role_bindings(): void
    {
        $subject = $this->subject();
        $admin   = $this->role('admin', 'Administrator');
        $editor  = $this->role('editor', 'Editor');

        $this->repository->create($subject, $admin, $editor);
        $this->repository->remove($subject, $admin);

        self::assertDatabaseMissing('subject_roles', [
            'role_id'           => $admin->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);

        self::assertDatabaseHas('subject_roles', [
            'role_id'           => $editor->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);
    }
}
