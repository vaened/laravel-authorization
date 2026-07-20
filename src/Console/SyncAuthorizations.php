<?php

declare(strict_types=1);

namespace Vaened\Authorization\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Vaened\Authorization\Configuration\Synchronization;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;
use Vaened\Sentinel\Errors\PermissionInUse;
use Vaened\Sentinel\Errors\RoleInUse;
use Vaened\Sentinel\Registry\PermissionRegistry;
use Vaened\Sentinel\Registry\RoleRegistry;
use Vaened\Sentinel\Repositories\RolePermissionRepository;
use Vaened\Sentinel\Role;

use function array_diff;
use function array_keys;
use function array_values;
use function count;
use function in_array;
use function sprintf;

final class SyncAuthorizations extends Command
{
    protected $signature   = 'authorization:sync {--prune : Remove roles and permissions not present in the configuration}';

    protected $description = 'Synchronize configured roles and permissions with the database';

    /**
     * @var array<string, int>
     */
    private array $stats = [
        'permissions_created' => 0,
        'permissions_updated' => 0,
        'roles_created'       => 0,
        'roles_updated'       => 0,
        'permissions_granted' => 0,
        'permissions_revoked' => 0,
        'permissions_pruned'  => 0,
        'roles_pruned'        => 0,
    ];

    public function handle(
        PermissionRegistry       $permissionRegistry,
        RoleRegistry             $roleRegistry,
        RolePermissionRepository $rolePermissions,
    ): int
    {
        /** @var array<string, array{name?: string, description?: string|null}> $permissionsConfig */
        $permissionsConfig = Synchronization::permissions();

        /** @var array<string, array{name?: string, description?: string|null, permissions?: list<string>}> $rolesConfig */
        $rolesConfig = Synchronization::roles();

        $this->validateRolePermissions($permissionsConfig, $rolesConfig);

        DB::transaction(function () use (
            $permissionRegistry,
            $roleRegistry,
            $rolePermissions,
            $permissionsConfig,
            $rolesConfig,
        ): void {
            $this->syncPermissions($permissionRegistry, $permissionsConfig);
            $this->syncRoles($permissionRegistry, $roleRegistry, $rolePermissions, $rolesConfig);

            if ($this->option('prune')) {
                $this->prune($permissionRegistry, $roleRegistry, $permissionsConfig, $rolesConfig);
            }
        });

        $this->summary();

        return self::SUCCESS;
    }

    /**
     * @param array<string, array{name?: string, description?: string|null}> $permissionsConfig
     * @param array<string, array{name?: string, description?: string|null, permissions?: list<string>}> $rolesConfig
     */
    private function validateRolePermissions(array $permissionsConfig, array $rolesConfig): void
    {
        $permissionCodes = array_keys($permissionsConfig);

        foreach ($rolesConfig as $roleCode => $roleConfig) {
            foreach ($roleConfig['permissions'] ?? [] as $permissionCode) {
                if (!in_array($permissionCode, $permissionCodes, true)) {
                    throw new InvalidArgumentException(sprintf(
                        'Role [%s] references permission [%s], but that permission is not defined.',
                        $roleCode,
                        $permissionCode,
                    ));
                }
            }
        }
    }

    /**
     * @param array<string, array{name?: string, description?: string|null}> $permissionsConfig
     */
    private function syncPermissions(PermissionRegistry $registry, array $permissionsConfig): void
    {
        foreach ($permissionsConfig as $code => $meta) {
            $name        = (string)($meta['name'] ?? $code);
            $description = $meta['description'] ?? null;
            $permission  = $registry->find($code);

            if ($permission === null) {
                $registry->create($code, $name, $description);
                $this->stats['permissions_created']++;
                $this->components->twoColumnDetail("permission $code", '<fg=green>created</>');
                continue;
            }

            if ($permission->name() !== $name || $permission->description() !== $description) {
                $registry->update($permission->id(), $name, $description);
                $this->stats['permissions_updated']++;
                $this->components->twoColumnDetail("permission $code", '<fg=yellow>updated</>');
            }
        }
    }

    /**
     * @param array<string, array{name?: string, description?: string|null, permissions?: list<string>}> $rolesConfig
     */
    private function syncRoles(
        PermissionRegistry       $permissionRegistry,
        RoleRegistry             $roleRegistry,
        RolePermissionRepository $rolePermissions,
        array                    $rolesConfig,
    ): void
    {
        foreach ($rolesConfig as $code => $meta) {
            $name        = (string)($meta['name'] ?? $code);
            $description = $meta['description'] ?? null;
            $role        = $roleRegistry->find($code);

            if ($role === null) {
                $role = $roleRegistry->create($code, $name, $description);
                $this->stats['roles_created']++;
                $this->components->twoColumnDetail("role $code", '<fg=green>created</>');
            } elseif ($role->name() !== $name || $role->description() !== $description) {
                $roleRegistry->update($role->id(), $name, $description);
                $this->stats['roles_updated']++;
                $this->components->twoColumnDetail("role $code", '<fg=yellow>updated</>');
            }

            $this->syncRolePermissions(
                $permissionRegistry,
                $rolePermissions,
                $role,
                $meta['permissions'] ?? [],
            );
        }
    }

    /**
     * @param list<string> $expectedCodes
     */
    private function syncRolePermissions(
        PermissionRegistry       $permissionRegistry,
        RolePermissionRepository $rolePermissions,
        Role                     $role,
        array                    $expectedCodes,
    ): void
    {
        $actualCodes = $rolePermissions->allOf($role)->codes();
        $toGrant     = array_values(array_diff($expectedCodes, $actualCodes));
        $toRevoke    = array_values(array_diff($actualCodes, $expectedCodes));

        if (count($toGrant) > 0) {
            $permissions = $permissionRegistry->lookup($toGrant)->values();
            Granter::grant($role, ...$permissions);
            $this->stats['permissions_granted'] += count($permissions);
            $this->components->twoColumnDetail(
                "role {$role->code()}",
                sprintf('<fg=green>+%d granted</>', count($permissions)),
            );
        }

        if (count($toRevoke) > 0) {
            $permissions = $permissionRegistry->lookup($toRevoke)->values();
            Revoker::revoke($role, ...$permissions);
            $this->stats['permissions_revoked'] += count($permissions);
            $this->components->twoColumnDetail(
                "role {$role->code()}",
                sprintf('<fg=red>-%d revoked</>', count($permissions)),
            );
        }
    }

    /**
     * @param array<string, array{name?: string, description?: string|null}> $permissionsConfig
     * @param array<string, array{name?: string, description?: string|null, permissions?: list<string>}> $rolesConfig
     */
    private function prune(
        PermissionRegistry $permissionRegistry,
        RoleRegistry       $roleRegistry,
        array              $permissionsConfig,
        array              $rolesConfig,
    ): void
    {
        $expectedRoleCodes       = array_keys($rolesConfig);
        $expectedPermissionCodes = array_keys($permissionsConfig);

        foreach ($this->orphanCodes(Tables::roles(), $expectedRoleCodes) as $code) {
            $role = $roleRegistry->find($code);

            if ($role === null) {
                continue;
            }

            try {
                $roleRegistry->remove($role->id());
                $this->stats['roles_pruned']++;
                $this->components->twoColumnDetail("role $code", '<fg=red>pruned</>');
            } catch (RoleInUse) {
                $this->warn("Role [$code] is in use; skipped.");
            }
        }

        foreach ($this->orphanCodes(Tables::permissions(), $expectedPermissionCodes) as $code) {
            $permission = $permissionRegistry->find($code);

            if ($permission === null) {
                continue;
            }

            try {
                $permissionRegistry->remove($permission->id());
                $this->stats['permissions_pruned']++;
                $this->components->twoColumnDetail("permission $code", '<fg=red>pruned</>');
            } catch (PermissionInUse) {
                $this->warn("Permission [$code] is in use; skipped.");
            }
        }
    }

    /**
     * @param list<string> $expectedCodes
     * @return list<string>
     */
    private function orphanCodes(string $table, array $expectedCodes): array
    {
        return array_values(array_diff(
            DB::table($table)->pluck('code')->all(),
            $expectedCodes,
        ));
    }

    private function summary(): void
    {
        $this->newLine();
        $this->table(
            ['Action', 'Permissions', 'Roles'],
            [
                ['Created', $this->stats['permissions_created'], $this->stats['roles_created']],
                ['Updated', $this->stats['permissions_updated'], $this->stats['roles_updated']],
                ['Granted', $this->stats['permissions_granted'], '-'],
                ['Revoked', $this->stats['permissions_revoked'], '-'],
                ['Pruned', $this->stats['permissions_pruned'], $this->stats['roles_pruned']],
            ],
        );
    }
}
