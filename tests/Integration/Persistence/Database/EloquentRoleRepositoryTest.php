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

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Vaened\Authorization\Errors\InvalidAuthorizationScope;
use Vaened\Authorization\Models\Role as RoleModel;
use Vaened\Authorization\Persistence\Database\EloquentRoleRepository;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Authorization\Tests\Runtime\NonSubjectModel;

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

        $roles = $this->repository->lookup(null, 'editor', 'auditor');

        self::assertCount(2, $roles);
        self::assertEqualsCanonicalizing(['editor', 'auditor'], $roles->codes());
    }

    public function test_lookup_returns_an_empty_collection_when_no_codes_are_provided(): void
    {
        $roles = $this->repository->lookup(null);

        self::assertCount(0, $roles);
        self::assertSame([], $roles->codes());
    }

    public function test_match_returns_roles_across_all_scopes(): void
    {
        $this->role('admin', 'Administrator');
        $this->role('editor', 'Editor');

        $roles = $this->repository->match('admin', 'editor');

        self::assertCount(2, $roles);
        self::assertEqualsCanonicalizing(['admin', 'editor'], $roles->codes());
    }

    public function test_lookup_and_create_support_scoped_roles(): void
    {
        $scope = $this->subject();
        $this->repository->create('admin', 'Global Administrator');
        $local = $this->repository->create('admin', 'Tenant Administrator', scope: $scope);

        self::assertSame($scope->getKey(), $local->scope()?->id());
        self::assertSame(['admin'], $this->repository->lookup($scope, 'admin')->codes());
        self::assertSame(['admin'], $this->repository->lookup(null, 'admin')->codes());
    }

    public function test_a_role_scope_is_loaded_only_once(): void
    {
        $scope      = $this->subject();
        $role       = $this->repository->create('admin', 'Tenant Administrator', scope: $scope);
        $queryCount = 0;

        DB::listen(static function () use (&$queryCount): void {
            $queryCount++;
        });

        self::assertSame($scope->getKey(), $role->scope()?->id());
        self::assertSame($scope->getKey(), $role->scope()?->id());

        self::assertSame(1, $queryCount);
    }

    public function test_a_role_with_a_non_subject_scope_throws(): void
    {
        $scope = NonSubjectModel::query()->create(['name' => 'Invalid scope']);
        $role  = RoleModel::query()->create([
            'code'       => 'admin',
            'name'       => 'Administrator',
            'scope_type' => $scope->getMorphClass(),
            'scope_id'   => $scope->getKey(),
        ]);

        $this->expectException(InvalidAuthorizationScope::class);
        $this->expectExceptionMessage('Expected an instance of [Vaened\\Sentinel\\Subject].');

        $role->scope();
    }

    public function test_a_role_code_is_unique_within_the_same_scope(): void
    {
        $scope = $this->subject();
        $this->repository->create('admin', 'Tenant Administrator', scope: $scope);

        $this->expectException(QueryException::class);

        $this->repository->create('admin', 'Duplicate Tenant Administrator', scope: $scope);
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
