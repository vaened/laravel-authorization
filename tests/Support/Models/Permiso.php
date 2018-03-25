<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Support\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Traits\IsPermission;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model implements PermissionContract
{
    use IsPermission;
}
