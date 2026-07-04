<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Database;

use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;

class PermissionEvaluator extends Evaluator
{
    public function evaluate(PermissionsOwner $owner, array $permissions): bool
    {
        $denied = $this->getDeniedPermissions($owner, $permissions);

        if ($denied->count() === count($permissions)) {
            return false;
        }

        $permissions = $this->clean($permissions, $denied);
        return $this->searchInRoles($owner, $permissions) || $this->has($owner->permissions()->getQuery())($permissions);
    }

    private function getDeniedPermissions(PermissionsOwner $owner, array $permissions): Collection
    {
        return $owner->permissions()->where('denied', true)->whereIn('secret_name', $permissions)->limit(count($permissions))->get();
    }

    private function clean(array $permissions, Collection $denied): array
    {
        $names = $denied->pluck('secret_name')->toArray();
        return array_filter($permissions, function (string $secret) use ($names): bool {
            return ! in_array($secret, $names);
        });
    }

    private function searchInRoles(PermissionsOwner $owner, array $permissions): bool
    {
        return $owner instanceof RolesOwner ? $this->hasPermissions($owner, $permissions) : false;
    }

    private function hasPermissions(RolesOwner $owner, array $permissions): bool
    {
        return $owner->roles()->limit(1)->whereHas('permissions', $this->same($permissions))->exists();
    }
}
