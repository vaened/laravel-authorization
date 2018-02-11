<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role extends Grantable, Permissible
{
    /**
     * Attach a permission to grantable.
     *
     * @param Permission $permission
     * @return bool
     */
    public function attach(Permission $permission): bool;

    /**
     * Detach a permission to grantable.
     *
     * @param Permission $permission
     * @return bool
     */
    public function detach(Permission $permission): bool;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getPermissionsRelationship(): BelongsToMany;
}
