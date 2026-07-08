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

use Illuminate\Support\Facades\DB;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Models\Permission as PermissionModel;
use Vaened\Sentinel\Operators\SubjectPermissionSnapshot;
use Vaened\Sentinel\Repositories\SubjectPermissionRepository as SubjectPermissionRepositoryContract;
use Vaened\Sentinel\Subject;
use Vaened\Sentinel\SubjectPermission;
use Vaened\Sentinel\SubjectPermissions;

final class EloquentSubjectPermissionRepository extends SubjectRepository implements SubjectPermissionRepositoryContract
{
    public function lookup(Subject $subject, string ...$codes): SubjectPermissions
    {
        if (empty($codes)) {
            return new SubjectPermissions([]);
        }

        return $this->subjectPermissionsOf($subject, $codes);
    }

    public function exists(int|string $permissionId): bool
    {
        return DB::table(Tables::subjectPermissions())
                 ->where('permission_id', $permissionId)
                 ->exists();
    }

    public function allOf(Subject $subject): SubjectPermissions
    {
        return $this->subjectPermissionsOf($subject);
    }

    public function create(Subject $subject, SubjectPermission ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::subjectPermissions())->insert(
            array_map(
                fn(SubjectPermission $permission): array => [
                    'permission_id'     => $permission->id(),
                    'authorizable_type' => $this->subjectType($subject),
                    'authorizable_id'   => $this->subjectId($subject),
                    'denied'            => $permission->isDenied(),
                ],
                $permissions
            )
        );
    }

    public function update(Subject $subject, SubjectPermission ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        $query = DB::table(Tables::subjectPermissions())
                   ->where('authorizable_type', $this->subjectType($subject))
                   ->where('authorizable_id', $this->subjectId($subject));

        foreach ($permissions as $permission) {
            (clone $query)
                ->where('permission_id', $permission->id())
                ->update(['denied' => $permission->isDenied()]);
        }
    }

    public function remove(Subject $subject, SubjectPermission ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::subjectPermissions())
          ->where('authorizable_type', $this->subjectType($subject))
          ->where('authorizable_id', $this->subjectId($subject))
          ->whereIn(
              'permission_id',
              array_map(static fn(SubjectPermission $permission): int|string => $permission->id(), $permissions)
          )
          ->delete();
    }

    protected function subjectPermissionsOf(Subject $subject, array $codes = []): SubjectPermissions
    {
        $query = PermissionModel::query()
                                ->select([
                                    Tables::permissions('id'),
                                    Tables::permissions('code'),
                                    Tables::permissions('name'),
                                    Tables::permissions('description'),
                                    Tables::subjectPermissions('denied') . ' as pivot_denied',
                                ])
                                ->join(
                                    Tables::subjectPermissions(),
                                    Tables::subjectPermissions('permission_id'),
                                    '=',
                                    Tables::permissions('id'),
                                )
                                ->where(Tables::subjectPermissions('authorizable_type'), $this->subjectType($subject))
                                ->where(Tables::subjectPermissions('authorizable_id'), $this->subjectId($subject));

        if (!empty($codes)) {
            $query->whereIn(Tables::permissions('code'), $codes);
        }

        return new SubjectPermissions(
            $query
                ->get()
                ->map(
                    static fn(PermissionModel $permission): SubjectPermissionSnapshot => new SubjectPermissionSnapshot(
                        $permission,
                        (bool)$permission->getAttributeValue('pivot_denied'),
                    )
                )
                ->all()
        );
    }
}
