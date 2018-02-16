<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Closure;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Exceptions\GrantableIsNotValidModelException;
use Enea\Authorization\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRole
{
    use Grantable, CanRefusePermission;

    public static function locateByName(string $secretName): ? RoleContract
    {
        return static::grantableBySecretName($secretName);
    }

    public function can(string $permission): bool
    {
        return true;
    }

    public function attach(PermissionContract $permission): bool
    {
        $this->syncAttach(Collection::make([$permission]));
        return $this->can($permission->getSecretName());
    }

    public function syncAttach(Collection $permissions): void
    {
        $this->getPermissionsRelationship()->saveMany($permissions);
    }

    public function detach(PermissionContract $permission): bool
    {
        $this->syncDetach(Collection::make([$permission]));
        return $this->cannot($permission->getSecretName());
    }

    public function syncDetach(Collection $permissions): void
    {
        $keys = $permissions->map($this->extractPermissionKeys())->toArray();
        $this->getPermissionsRelationship()->detach($keys);
    }

    public function getPermissionsRelationship(): BelongsToMany
    {
        return $this->belongsToMany(Tables::permissionModel(), Tables::permissionName(), 'permission_id', 'id');
    }

    private function extractPermissionKeys(): Closure
    {
        return function (PermissionContract $permission) {
            if (! $permission instanceof Model) {
                $this->throwInvalidPermissionError($permission);
            }

            return $permission->getKey();
        };
    }

    private function throwInvalidPermissionError(PermissionContract $permission): void
    {
        throw GrantableIsNotValidModelException::make($permission);
    }
}
