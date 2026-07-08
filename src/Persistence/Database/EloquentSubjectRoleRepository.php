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
use Vaened\Authorization\Models\Role;
use Vaened\Sentinel\Permissions;
use Vaened\Sentinel\Repositories\SubjectRoleRepository as SubjectRoleRepositoryContract;
use Vaened\Sentinel\Role as RoleContract;
use Vaened\Sentinel\Roles;
use Vaened\Sentinel\Subject;

final class EloquentSubjectRoleRepository extends SubjectRepository implements SubjectRoleRepositoryContract
{
    public function lookup(Subject $subject, string ...$codes): Roles
    {
        if (empty($codes)) {
            return new Roles([]);
        }

        return new Roles(
            $this->rolesOf($subject)
                 ->whereIn(Tables::roles('code'), $codes)
                 ->get()
                 ->all()
        );
    }

    public function grants(Subject $subject, string ...$codes): Permissions
    {
        if (empty($codes)) {
            return new Permissions([]);
        }

        return new Permissions(
            PermissionModel::query()
                           ->select(
                               Tables::permissions('id'),
                               Tables::permissions('code'),
                               Tables::permissions('name'),
                               Tables::permissions('description'),
                           )
                           ->join(
                               Tables::rolePermissions(),
                               Tables::rolePermissions('permission_id'),
                               '=',
                               Tables::permissions('id'),
                           )
                           ->join(
                               Tables::subjectRoles(),
                               Tables::subjectRoles('role_id'),
                               '=',
                               Tables::rolePermissions('role_id'),
                           )
                           ->where(Tables::subjectRoles('authorizable_type'), $this->subjectType($subject))
                           ->where(Tables::subjectRoles('authorizable_id'), $this->subjectId($subject))
                           ->whereIn(Tables::permissions('code'), $codes)
                           ->distinct()
                           ->get()
                           ->all()
        );
    }

    public function exists(int|string $roleId): bool
    {
        return DB::table(Tables::subjectRoles())
                 ->where('role_id', $roleId)
                 ->exists();
    }

    public function create(Subject $subject, RoleContract ...$roles): void
    {
        if (empty($roles)) {
            return;
        }

        DB::table(Tables::subjectRoles())->insert(
            array_map(
                fn(RoleContract $role): array => [
                    'role_id'           => $role->id(),
                    'authorizable_type' => $this->subjectType($subject),
                    'authorizable_id'   => $this->subjectId($subject),
                ],
                $roles
            )
        );
    }

    public function remove(Subject $subject, RoleContract ...$roles): void
    {
        if (empty($roles)) {
            return;
        }

        DB::table(Tables::subjectRoles())
          ->where('authorizable_type', $this->subjectType($subject))
          ->where('authorizable_id', $this->subjectId($subject))
          ->whereIn(
              'role_id',
              array_map(static fn(RoleContract $role): int|string => $role->id(), $roles)
          )
          ->delete();
    }

    protected function rolesOf(Subject $subject): Builder
    {
        return Role::query()
                   ->select(
                       Tables::roles('id'),
                       Tables::roles('code'),
                       Tables::roles('name'),
                       Tables::roles('description')
                   )
                   ->join(
                       Tables::subjectRoles(),
                       Tables::subjectRoles('role_id'),
                       '=',
                       Tables::roles('id'),
                   )
                   ->where(Tables::subjectRoles('authorizable_type'), $this->subjectType($subject))
                   ->where(Tables::subjectRoles('authorizable_id'), $this->subjectId($subject));
    }
}
