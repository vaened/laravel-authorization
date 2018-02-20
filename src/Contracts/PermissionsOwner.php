<?php
/**
 * Created on 16/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermissionsOwner extends GrantableOwner
{
    public function permissions(): BelongsToMany;

    public function getPermissionModels(): EloquentCollection;
}
