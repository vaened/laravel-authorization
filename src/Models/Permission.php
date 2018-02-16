<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Traits\HasPermission;

class Permission extends Grantable implements PermissionContract
{
    use HasPermission;

    protected $configTableKeyName = 'permission';
}
