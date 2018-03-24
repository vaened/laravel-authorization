<?php
declare(strict_types=1);

/**
 * Created on 17/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RolesOwner extends GrantableOwner
{
    public function roles(): BelongsToMany;

    public function getRoleModels(): EloquentCollection;
}
