<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleContract extends Grantable, Permissible
{
    /**
     * Attach a permission to grantable.
     *
     * @param PermissionContract $permission
     * @return bool
     */
    public function attach(PermissionContract $permission): bool;

    /**
     * Detach a permission to grantable.
     *
     * @param PermissionContract $permission
     * @return bool
     */
    public function detach(PermissionContract $permission): bool;

    /**
     * Attach one more permissions to grantable.
     *
     * @param Collection $permissions
     * @return void
     */
    public function syncAttach(Collection $permissions): void;

    /**
     * Detach one more permissions to grantable.
     *
     * @param Collection $permissions
     * @return void
     */
    public function syncDetach(Collection $permissions): void;

    /**
     * Returns the relation with all permissions.
     *
     * @return BelongsToMany
     */
    public function getPermissionsRelationship(): BelongsToMany;
}
