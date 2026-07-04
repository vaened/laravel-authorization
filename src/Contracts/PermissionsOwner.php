<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface PermissionsOwner extends Owner
{
    public function permissions(): BelongsToMany;

    public function getPermissionModels(): EloquentCollection;
}
