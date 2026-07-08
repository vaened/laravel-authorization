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

use Vaened\Authorization\Persistence\Database\EloquentRoleRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;

final class EloquentRoleRepositoryTest extends DatabaseTestCase
{
    private EloquentRoleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new EloquentRoleRepository();
    }

    public function test_lookup_returns_only_matching_roles(): void
    {
        $this->role('admin', 'Administrator');
        $this->role('editor', 'Editor');
        $this->role('auditor', 'Auditor');

        $roles = $this->repository->lookup('editor', 'auditor');

        self::assertCount(2, $roles);
        self::assertEqualsCanonicalizing(['editor', 'auditor'], $roles->codes());
    }

    public function test_lookup_returns_an_empty_collection_when_no_codes_are_provided(): void
    {
        $roles = $this->repository->lookup();

        self::assertCount(0, $roles);
        self::assertSame([], $roles->codes());
    }

    public function test_exists_returns_true_only_for_persisted_roles(): void
    {
        $role = $this->role('admin', 'Administrator');

        self::assertTrue($this->repository->exists($role->id()));
        self::assertFalse($this->repository->exists(999_999));
    }

    public function test_create_persists_a_role_with_its_attributes(): void
    {
        $role = $this->repository->create('admin', 'Administrator', 'Full access');

        self::assertSame('admin', $role->code());
        self::assertSame('Administrator', $role->name());
        self::assertSame('Full access', $role->description());
        self::assertDatabaseHas('roles', [
            'id'          => $role->id(),
            'code'        => 'admin',
            'name'        => 'Administrator',
            'description' => 'Full access',
        ]);
    }

    public function test_update_changes_name_and_description_of_an_existing_role(): void
    {
        $role = $this->role('admin', 'Administrator', 'Before');

        $this->repository->update($role->id(), 'Owner', 'After');

        self::assertDatabaseHas('roles', [
            'id'          => $role->id(),
            'code'        => 'admin',
            'name'        => 'Owner',
            'description' => 'After',
        ]);
    }

    public function test_remove_deletes_the_role(): void
    {
        $role = $this->role('admin', 'Administrator');

        $this->repository->remove($role->id());

        self::assertDatabaseMissing('roles', [
            'id' => $role->id(),
        ]);
    }
}
