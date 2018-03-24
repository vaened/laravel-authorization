<?php

declare(strict_types=1);

/**
 * Created on 15/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Support\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Traits\HasPermission;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model implements PermissionContract
{
    use HasPermission;
}
