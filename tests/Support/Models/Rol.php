<?php
declare(strict_types=1);

/**
 * Created on 15/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Support\Models;

use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Traits\HasRole;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model implements RoleContract
{
    use HasRole;
}
