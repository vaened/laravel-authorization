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
use Vaened\Authorization\Models\Role as RoleModel;
use Vaened\Authorization\Persistence\SubjectRepository;
use Vaened\Sentinel\Repositories\RoleRepository as RoleRepositoryContract;
use Vaened\Sentinel\Role;
use Vaened\Sentinel\Roles;
use Vaened\Sentinel\Subject;

final class EloquentRoleRepository extends SubjectRepository implements RoleRepositoryContract
{
    public function lookup(Subject|null $scope, string ...$codes): Roles
    {
        if (empty($codes)) {
            return new Roles([]);
        }

        $query = RoleModel::query()->whereIn('code', $codes);
        $this->constrainScope($query, $scope);

        return new Roles($query->get()->all());
    }

    public function match(string ...$codes): Roles
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

    public function create(
        string       $code,
        string       $name,
        string|null  $description = null,
        Subject|null $scope = null,
    ): Role
    {
        $attributes = [
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
        ];

        if ($scope !== null) {
            $attributes['scope_type'] = $this->subjectType($scope);
            $attributes['scope_id']   = $this->subjectId($scope);
        }

        return RoleModel::query()->create($attributes);
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

    private function constrainScope(Builder $query, Subject|null $scope): void
    {
        if ($scope === null) {
            $query->whereNull('scope_type')->whereNull('scope_id');
            return;
        }

        $query->where('scope_type', $this->subjectType($scope))
              ->where('scope_id', $this->subjectId($scope));
    }
}
