<?php
/**
 * Created on 17/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleAndPermissionOwner extends PermissionOwner
{
    public function roles(): BelongsToMany;
}
