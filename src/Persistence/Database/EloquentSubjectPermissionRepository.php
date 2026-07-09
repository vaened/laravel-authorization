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
use Vaened\Authorization\Persistence\SubjectRepository;
use Vaened\Sentinel\Operators\SubjectPermissionSnapshot;
use Vaened\Sentinel\Repositories\SubjectPermissionRepository as SubjectPermissionRepositoryContract;
use Vaened\Sentinel\Subject;
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

    public function create(Subject $subject, SubjectPermissionSnapshot ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::subjectPermissions())->insert(
            array_map(
                fn(SubjectPermissionSnapshot $permission): array => [
                    'permission_id'     => $permission->permissionId(),
                    'authorizable_type' => $this->subjectType($subject),
                    'authorizable_id'   => $this->subjectId($subject),
                    'denied'            => $permission->isDenied(),
                ],
                $permissions
            )
        );
    }

    public function update(Subject $subject, SubjectPermissionSnapshot ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        $granted = [];
        $denied  = [];

        foreach ($permissions as $permission) {
            if ($permission->isDenied()) {
                $denied[] = $permission->permissionId();
            } else {
                $granted[] = $permission->permissionId();
            }
        }

        $query = DB::table(Tables::subjectPermissions())
                   ->where('authorizable_type', $this->subjectType($subject))
                   ->where('authorizable_id', $this->subjectId($subject));

        if (!empty($granted)) {
            $query->clone()->whereIn('permission_id', $granted)->update(['denied' => false]);
        }

        if (!empty($denied)) {
            $query->clone()->whereIn('permission_id', $denied)->update(['denied' => true]);
        }
    }

    public function remove(Subject $subject, SubjectPermissionSnapshot ...$permissions): void
    {
        if (empty($permissions)) {
            return;
        }

        DB::table(Tables::subjectPermissions())
          ->where('authorizable_type', $this->subjectType($subject))
          ->where('authorizable_id', $this->subjectId($subject))
          ->whereIn(
              'permission_id',
              array_map(static fn(SubjectPermissionSnapshot $permission): int|string => $permission->permissionId(), $permissions)
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
                        $permission->id(),
                        $permission->code(),
                        (bool)$permission->getAttributeValue('pivot_denied'),
                    )
                )
                ->all()
        );
    }
}
