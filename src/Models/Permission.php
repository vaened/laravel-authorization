<?php

declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Traits\HasPermission;

class Permission extends Grantable implements PermissionContract
{
    use HasPermission;

    protected function getConfigTableName(): string
    {
        return Config::permissionTableName();
    }
}
