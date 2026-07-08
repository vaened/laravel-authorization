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

use Vaened\Authorization\Persistence\Database\EloquentSubjectPermissionRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Sentinel\Operators\SubjectPermissionSnapshot;

final class EloquentSubjectPermissionRepositoryTest extends DatabaseTestCase
{
    private EloquentSubjectPermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentSubjectPermissionRepository();
    }

    public function test_lookup_returns_only_requested_permissions_with_their_denied_state(): void
    {
        $subject      = $this->subject();
        $readUsers    = $this->permission('users.read', 'Read Users');
        $updateUsers  = $this->permission('users.update', 'Update Users');
        $deleteUsers  = $this->permission('users.delete', 'Delete Users');

        $this->repository->create(
            $subject,
            new SubjectPermissionSnapshot($readUsers),
            new SubjectPermissionSnapshot($updateUsers, true),
        );

        $otherSubject = $this->subject();
        $this->repository->create($otherSubject, new SubjectPermissionSnapshot($deleteUsers, true));

        $permissions = $this->repository->lookup($subject, 'users.update', 'users.delete');

        self::assertCount(1, $permissions);
        self::assertSame(['users.update'], $permissions->codes());
        self::assertTrue($permissions->find('users.update')?->isDenied());
    }

    public function test_exists_reports_when_a_permission_is_assigned_to_any_subject(): void
    {
        $subject     = $this->subject();
        $permission  = $this->permission('users.read', 'Read Users');

        $this->repository->create($subject, new SubjectPermissionSnapshot($permission));

        self::assertTrue($this->repository->exists($permission->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_all_of_returns_every_permission_assigned_to_the_subject_with_denied_flags(): void
    {
        $subject      = $this->subject();
        $readUsers    = $this->permission('users.read', 'Read Users');
        $updateUsers  = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            new SubjectPermissionSnapshot($readUsers),
            new SubjectPermissionSnapshot($updateUsers, true),
        );

        $permissions = $this->repository->allOf($subject);

        self::assertCount(2, $permissions);
        self::assertFalse($permissions->find('users.read')?->isDenied());
        self::assertTrue($permissions->find('users.update')?->isDenied());
    }

    public function test_create_persists_subject_permissions_with_their_denied_state(): void
    {
        $subject     = $this->subject();
        $permission  = $this->permission('users.read', 'Read Users');

        $this->repository->create($subject, new SubjectPermissionSnapshot($permission, true));

        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $permission->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => true,
        ]);
    }

    public function test_update_changes_only_the_requested_denied_flags(): void
    {
        $subject      = $this->subject();
        $readUsers    = $this->permission('users.read', 'Read Users');
        $updateUsers  = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            new SubjectPermissionSnapshot($readUsers, false),
            new SubjectPermissionSnapshot($updateUsers, false),
        );

        $this->repository->update($subject, new SubjectPermissionSnapshot($updateUsers, true));

        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $readUsers->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => false,
        ]);

        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $updateUsers->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => true,
        ]);
    }

    public function test_remove_deletes_only_the_requested_subject_permissions(): void
    {
        $subject      = $this->subject();
        $readUsers    = $this->permission('users.read', 'Read Users');
        $updateUsers  = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            new SubjectPermissionSnapshot($readUsers),
            new SubjectPermissionSnapshot($updateUsers, true),
        );

        $this->repository->remove($subject, new SubjectPermissionSnapshot($readUsers));

        self::assertDatabaseMissing('subject_permissions', [
            'permission_id'     => $readUsers->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);

        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $updateUsers->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => true,
        ]);
    }
}
