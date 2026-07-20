<?php

declare(strict_types=1);

namespace Vaened\Authorization\Tests\Integration\Console;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Symfony\Component\Console\Output\BufferedOutput;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Tests\DatabaseTestCase;

final class SyncAuthorizationsTest extends DatabaseTestCase
{
    public function test_it_syncs_configured_permissions_roles_and_role_permissions(): void
    {
        $this->setAuthorizationsConfig([
            'permissions' => [
                'users.read'  => ['name' => 'Read users'],
                'users.write' => ['name' => 'Write users'],
            ],
            'roles'       => [
                'editor' => [
                    'name'        => 'Editor',
                    'permissions' => ['users.read', 'users.write'],
                ],
            ],
        ]);

        $this->artisan('authorization:sync')
             ->expectsOutputToContain('permission users.read')
             ->expectsTable(
                 ['Action', 'Permissions', 'Roles'],
                 [
                     ['Created', 2, 1],
                     ['Updated', 0, 0],
                     ['Granted', 2, '-'],
                     ['Revoked', 0, '-'],
                     ['Pruned', 0, 0],
                 ],
             )
             ->assertSuccessful();
        $this->artisan('authorization:sync')->assertSuccessful();

        self::assertDatabaseCount('permissions', 2);
        self::assertDatabaseCount('roles', 1);
        self::assertDatabaseCount('role_permissions', 2);
    }

    public function test_it_updates_metadata_and_reconciles_role_permissions(): void
    {
        $this->setAuthorizationsConfig([
            'permissions' => [
                'users.read'  => ['name' => 'Read users'],
                'users.write' => ['name' => 'Write users'],
            ],
            'roles'       => [
                'editor' => [
                    'name'        => 'Editor',
                    'permissions' => ['users.read', 'users.write'],
                ],
            ],
        ]);

        $this->artisan('authorization:sync')->assertSuccessful();

        $this->setAuthorizationsConfig([
            'permissions' => [
                'users.read'  => ['name' => 'Read user accounts', 'description' => 'Updated'],
                'users.write' => ['name' => 'Write users'],
            ],
            'roles'       => [
                'editor' => ['name' => 'Content editor', 'permissions' => ['users.read']],
            ],
        ]);

        $output = $this->runSynchronization();

        self::assertStringContainsString('permission users.read', $output);
        self::assertStringContainsString('updated', $output);
        self::assertStringContainsString('role editor', $output);
        self::assertStringContainsString('revoked', $output);

        self::assertDatabaseHas('permissions', [
            'code'        => 'users.read',
            'name'        => 'Read user accounts',
            'description' => 'Updated',
        ]);
        self::assertDatabaseHas('roles', [
            'code' => 'editor',
            'name' => 'Content editor',
        ]);
        self::assertDatabaseCount('role_permissions', 1);
    }

    public function test_it_only_prunes_entries_when_requested(): void
    {
        DB::table(Tables::permissions())->insert([
            'code' => 'legacy.read',
            'name' => 'Legacy read',
        ]);
        DB::table(Tables::roles())->insert([
            'code' => 'legacy',
            'name' => 'Legacy',
        ]);

        $this->setAuthorizationsConfig([]);

        $this->artisan('authorization:sync')->assertSuccessful();
        self::assertDatabaseCount('permissions', 1);
        self::assertDatabaseCount('roles', 1);

        $output = $this->runSynchronization(['--prune' => true]);

        self::assertStringContainsString('permission legacy.read', $output);
        self::assertStringContainsString('pruned', $output);
        self::assertStringContainsString('role legacy', $output);
        self::assertDatabaseCount('permissions', 0);
        self::assertDatabaseCount('roles', 0);
    }

    public function test_it_warns_when_pruning_entries_that_are_in_use(): void
    {
        $subject    = $this->subject();
        $role       = $this->role('legacy', 'Legacy');
        $permission = $this->permission('legacy.read', 'Legacy read');

        $subject->grant($role);
        $subject->grant($permission);
        $this->setAuthorizationsConfig([]);

        $this->artisan('authorization:sync', ['--prune' => true])
             ->expectsOutputToContain('Role [legacy] is in use; skipped.')
             ->expectsOutputToContain('Permission [legacy.read] is in use; skipped.')
             ->assertSuccessful();

        self::assertDatabaseHas('roles', ['code' => 'legacy']);
        self::assertDatabaseHas('permissions', ['code' => 'legacy.read']);
    }

    public function test_it_rejects_roles_that_reference_undefined_permissions(): void
    {
        $this->setAuthorizationsConfig([
            'permissions' => [],
            'roles'       => [
                'editor' => [
                    'name'        => 'Editor',
                    'permissions' => ['users.read'],
                ],
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->artisan('authorization:sync');
    }

    public function test_it_rejects_permissions_without_the_required_name(): void
    {
        $this->setAuthorizationsConfig([
            'permissions' => [
                'users.read' => [],
            ],
            'roles'       => [],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('permissions.users.read.name');
        $this->expectExceptionMessage('Expected syntax:');

        $this->artisan('authorization:sync');
    }

    public function test_it_rejects_roles_without_at_least_one_permission(): void
    {
        $this->setAuthorizationsConfig([
            'permissions' => [
                'users.read' => ['name' => 'Read users'],
            ],
            'roles'       => [
                'editor' => [
                    'name'        => 'Editor',
                    'permissions' => [],
                ],
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('roles.editor.permissions');
        $this->expectExceptionMessage('Expected syntax:');

        $this->artisan('authorization:sync');
    }

    public function test_it_uses_the_configured_authorization_file_key(): void
    {
        config()->set('authorization.synchronization.config', 'access_control');
        config()->set('access_control', [
            'permissions' => [
                'users.read' => ['name' => 'Read users'],
            ],
            'roles'       => [],
        ]);

        $this->artisan('authorization:sync')->assertSuccessful();

        self::assertDatabaseHas('permissions', [
            'code' => 'users.read',
        ]);
    }

    public function test_it_fails_when_the_configured_authorization_file_key_does_not_exist(): void
    {
        config()->set('authorization.synchronization.config', 'missing_authorizations');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing_authorizations');

        $this->artisan('authorization:sync');
    }

    /**
     * @param array{permissions?: array<string, mixed>, roles?: array<string, mixed>} $config
     */
    private function setAuthorizationsConfig(array $config): void
    {
        config()->set('authorizations', array_replace([
            'permissions' => [],
            'roles'       => [],
        ], $config));
    }

    /** @param array<string, mixed> $parameters */
    private function runSynchronization(array $parameters = []): string
    {
        $output = new BufferedOutput();

        self::assertSame(0, $this->app->make(Kernel::class)->call('authorization:sync', $parameters, $output));

        return $output->fetch();
    }
}
