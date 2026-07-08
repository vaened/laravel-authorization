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

use Vaened\Authorization\Models\Role as RoleModel;
use Vaened\Sentinel\Repositories\RoleRepository as RoleRepositoryContract;
use Vaened\Sentinel\Role;
use Vaened\Sentinel\Roles;

final class EloquentRoleRepository implements RoleRepositoryContract
{
    public function lookup(string ...$codes): Roles
    {
        if (empty($codes)) {
            return new Roles([]);
        }

        return new Roles(RoleModel::query()->whereIn('code', $codes)->get()->all());
    }

    public function exists(int|string $id): bool
    {
        return RoleModel::query()->whereKey($id)->exists();
    }

    public function create(string $code, string $name, string|null $description = null): Role
    {
        return RoleModel::query()->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function update(int|string $id, string $name, string|null $description = null): void
    {
        RoleModel::query()->whereKey($id)->update([
            'name'        => $name,
            'description' => $description,
        ]);
    }

    public function remove(int|string $id): void
    {
        RoleModel::query()->whereKey($id)->delete();
    }
}
