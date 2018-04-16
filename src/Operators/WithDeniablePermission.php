<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Facades\Helper;
use Illuminate\Support\Collection;

trait WithDeniablePermission
{
    abstract protected function isModifiable(PermissionContract $permission): bool;

    abstract protected function throwException(Grantable $grantable): void;

    private function getPermissions(PermissionsOwner $owner, Collection $permissions): Collection
    {
        return $owner->permissions()->whereKey($permissions->pluck('id'))->limit(count($permissions))->get();
    }

    private function except(Collection $granted, Collection $permissions): Collection
    {
        return Helper::except($permissions, $granted->pluck('secret_name')->toArray());
    }

    private function denialStatus(PermissionsOwner $owner, bool $denied): Closure
    {
        return function (PermissionContract $permission) use ($owner, $denied): void {
            if (! $this->isModifiable($permission)) {
                return;
            }

            $result = $owner->permissions()->updateExistingPivot($permission->getIdentificationKey(), [
                'denied' => $denied
            ]);

            if (! $this->isSuccessful($result)) {
                $this->throwException($permission);
            }
        };
    }

    private function isSuccessful(int $results): bool
    {
        return $results > 0;
    }
}
