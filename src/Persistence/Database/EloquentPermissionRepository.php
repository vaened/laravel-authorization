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

use Vaened\Authorization\Models\Permission as PermissionModel;
use Vaened\Sentinel\Permission;
use Vaened\Sentinel\Permissions;
use Vaened\Sentinel\Repositories\PermissionRepository as PermissionRepositoryContract;

final class EloquentPermissionRepository implements PermissionRepositoryContract
{
    public function lookup(string ...$codes): Permissions
    {
        if (empty($codes)) {
            return new Permissions([]);
        }

        return new Permissions(PermissionModel::query()->whereIn('code', $codes)->get()->all());
    }

    public function exists(int|string $id): bool
    {
        return PermissionModel::query()->whereKey($id)->exists();
    }

    public function create(string $code, string $name, string|null $description = null): Permission
    {
        return PermissionModel::query()->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function update(int|string $id, string $name, string|null $description = null): void
    {
        PermissionModel::query()->whereKey($id)->update([
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function remove(int|string $id): void
    {
        PermissionModel::query()->whereKey($id)->delete();
    }
}
