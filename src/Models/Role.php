<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Traits\HasRole;

class Role extends Grantable implements RoleContract
{
    use HasRole;

    protected $configTableKeyName = 'roles';
}
