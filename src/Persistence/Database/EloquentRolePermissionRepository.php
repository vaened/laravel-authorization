<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Persistence\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Models\Permission as PermissionModel;
use Vaened\Sentinel\Permission as PermissionContract;
use Vaened\Sentinel\Permissions;
use Vaened\Sentinel\Repositories\RolePermissionRepository as RolePermissionRepositoryContract;
use Vaened\Sentinel\Role;

final class EloquentRolePermissionRepository implements RolePermissionRepositoryContract
{
    public function lookup(Role $role, string ...$codes): Permissions
    {
        if (empty($codes)) {
            return new Permissions([]);
        }

        return new Permissions(
            $this->permissionsOf($role)
                 ->whereIn(Tables::permissions('code'), $codes)
                 ->get()
                 ->all()
        );
    }

    public function exists(int|string $permissionId): bool
    {
        return DB::table(Tables::rolePermissions())
                 ->where('permission_id', $permissionId)
                 ->exists();
    }

    public function allOf(Role $role): Permissions
    {
        return new Permissions($this->permissionsOf($role)->get()->all());
    }

    public function create(Role $role, PermissionContract ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::rolePermissions())->insert(
            array_map(
                static fn(PermissionContract $permission): array => [
                    'role_id' => $role->id(),
                    'permission_id' => $permission->id(),
                ],
                $permissions
            )
        );
    }

    public function remove(Role $role, PermissionContract ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::rolePermissions())
          ->where('role_id', $role->id())
          ->whereIn(
              'permission_id',
              array_map(static fn(PermissionContract $permission): int|string => $permission->id(), $permissions)
          )
          ->delete();
    }

    protected function permissionsOf(Role $role): Builder
    {
        return PermissionModel::query()
                              ->select(Tables::permissions('id'),
                                  Tables::permissions('code'),
                                  Tables::permissions('name'),
                                  Tables::permissions('description'))
                              ->join(
                                  Tables::rolePermissions(),
                                  Tables::rolePermissions('permission_id'),
                                  '=',
                                  Tables::permissions('id'),
                              )
                              ->where(Tables::rolePermissions('role_id'), $role->id());
    }
}
