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

use Vaened\Authorization\Persistence\Database\EloquentPermissionRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;

final class EloquentPermissionRepositoryTest extends DatabaseTestCase
{
    private EloquentPermissionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentPermissionRepository();
    }

    public function test_lookup_returns_only_matching_permissions(): void
    {
        $this->permission('users.read', 'Read Users');
        $this->permission('users.update', 'Update Users');
        $this->permission('users.delete', 'Delete Users');

        $permissions = $this->repository->lookup('users.update', 'users.delete');

        self::assertCount(2, $permissions);
        self::assertEqualsCanonicalizing(['users.update', 'users.delete'], $permissions->codes());
    }

    public function test_lookup_returns_an_empty_collection_when_no_codes_are_provided(): void
    {
        $permissions = $this->repository->lookup();

        self::assertCount(0, $permissions);
        self::assertSame([], $permissions->codes());
    }

    public function test_exists_returns_true_only_for_persisted_permissions(): void
    {
        $permission = $this->permission('users.read', 'Read Users');

        self::assertTrue($this->repository->exists($permission->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_create_persists_a_permission_with_its_attributes(): void
    {
        $permission = $this->repository->create('users.read', 'Read Users', 'Can read any user');

        self::assertSame('users.read', $permission->code());
        self::assertSame('Read Users', $permission->name());
        self::assertSame('Can read any user', $permission->description());
        self::assertDatabaseHas('permissions', [
            'id'          => $permission->id(),
            'code'        => 'users.read',
            'name'        => 'Read Users',
            'description' => 'Can read any user',
        ]);
    }

    public function test_update_changes_name_and_description_of_an_existing_permission(): void
    {
        $permission = $this->permission('users.read', 'Read Users', 'Before');

        $this->repository->update($permission->id(), 'See Users', 'After');

        self::assertDatabaseHas('permissions', [
            'id'          => $permission->id(),
            'code'        => 'users.read',
            'name'        => 'See Users',
            'description' => 'After',
        ]);
    }

    public function test_remove_deletes_the_permission(): void
    {
        $permission = $this->permission('users.read', 'Read Users');

        $this->repository->remove($permission->id());

        self::assertDatabaseMissing('permissions', [
            'id' => $permission->id(),
        ]);
    }
}
