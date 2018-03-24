<?php

declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Traits\HasRole;

class Role extends Grantable implements RoleContract
{
    use HasRole;

    protected function getConfigTableName(): string
    {
        return Config::roleTableName();
    }
}
