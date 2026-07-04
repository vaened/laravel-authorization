<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RolesOwner extends Owner
{
    public function roles(): BelongsToMany;

    public function getRoleModels(): EloquentCollection;
}
