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

use Illuminate\Support\Facades\DB;
use Vaened\Authorization\Persistence\Database\EloquentSubjectPermissionRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Sentinel\Operators\SubjectPermissionSnapshot;
use Vaened\Sentinel\SubjectPermissionState;

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
        $subject     = $this->subject();
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');
        $deleteUsers = $this->permission('users.delete', 'Delete Users');

        $this->repository->create(
            $subject,
            SubjectPermissionSnapshot::from($readUsers),
            SubjectPermissionSnapshot::from($updateUsers, true),
        );

        $otherSubject = $this->subject();
        $this->repository->create($otherSubject, SubjectPermissionSnapshot::from($deleteUsers, true));

        $permissions = $this->repository->lookup($subject, 'users.update', 'users.delete');

        self::assertCount(1, $permissions);
        self::assertSame(['users.update'], $permissions->codes());
        self::assertSame(SubjectPermissionState::Denied, $permissions->find('users.update')?->state());
    }

    public function test_exists_reports_when_a_permission_is_assigned_to_any_subject(): void
    {
        $subject    = $this->subject();
        $permission = $this->permission('users.read', 'Read Users');

        $this->repository->create($subject, SubjectPermissionSnapshot::from($permission));

        self::assertTrue($this->repository->exists($permission->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_all_of_returns_every_permission_assigned_to_the_subject_with_denied_flags(): void
    {
        $subject     = $this->subject();
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            SubjectPermissionSnapshot::from($readUsers),
            SubjectPermissionSnapshot::from($updateUsers, true),
        );

        $permissions = $this->repository->allOf($subject);

        self::assertCount(2, $permissions);
        self::assertSame(SubjectPermissionState::Direct, $permissions->find('users.read')?->state());
        self::assertSame(SubjectPermissionState::Denied, $permissions->find('users.update')?->state());
    }

    public function test_create_persists_subject_permissions_with_their_denied_state(): void
    {
        $subject    = $this->subject();
        $permission = $this->permission('users.read', 'Read Users');

        $this->repository->create($subject, SubjectPermissionSnapshot::from($permission, true));

        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $permission->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => true,
        ]);
    }

    public function test_update_changes_only_the_requested_denied_flags(): void
    {
        $subject     = $this->subject();
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            SubjectPermissionSnapshot::from($readUsers, false),
            SubjectPermissionSnapshot::from($updateUsers, false),
        );

        $this->repository->update($subject, SubjectPermissionSnapshot::from($updateUsers, true));

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

    public function test_update_with_mixed_snapshots_groups_them_into_at_most_two_queries(): void
    {
        $subject     = $this->subject();
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');
        $deleteUsers = $this->permission('users.delete', 'Delete Users');

        $this->repository->create(
            $subject,
            SubjectPermissionSnapshot::from($readUsers, false),
            SubjectPermissionSnapshot::from($updateUsers, false),
            SubjectPermissionSnapshot::from($deleteUsers, false),
        );

        $captured = [];
        DB::listen(static function ($query) use (&$captured): void {
            if (str_contains($query->sql, 'subject_permissions')) {
                $captured[] = $query->sql;
            }
        });

        $this->repository->update(
            $subject,
            SubjectPermissionSnapshot::from($readUsers, true),
            SubjectPermissionSnapshot::from($updateUsers, false),
            SubjectPermissionSnapshot::from($deleteUsers, true),
        );

        self::assertCount(2, $captured);

        $this->assertDatabaseHas('subject_permissions', [
            'permission_id'   => $readUsers->id(),
            'authorizable_id' => $subject->id(),
            'denied'          => true,
        ]);
        $this->assertDatabaseHas('subject_permissions', [
            'permission_id'   => $updateUsers->id(),
            'authorizable_id' => $subject->id(),
            'denied'          => false,
        ]);
        $this->assertDatabaseHas('subject_permissions', [
            'permission_id'   => $deleteUsers->id(),
            'authorizable_id' => $subject->id(),
            'denied'          => true,
        ]);
    }

    public function test_remove_deletes_only_the_requested_subject_permissions(): void
    {
        $subject     = $this->subject();
        $readUsers   = $this->permission('users.read', 'Read Users');
        $updateUsers = $this->permission('users.update', 'Update Users');

        $this->repository->create(
            $subject,
            SubjectPermissionSnapshot::from($readUsers),
            SubjectPermissionSnapshot::from($updateUsers, true),
        );

        $this->repository->remove($subject, SubjectPermissionSnapshot::from($readUsers));

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
