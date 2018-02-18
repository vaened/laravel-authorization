<?php
/**
 * Created on 16/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermissionOwner extends GrantableOwner
{
    public function permissions(): BelongsToMany;
}
