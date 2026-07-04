<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Support\Models;

use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Traits\IsPermission;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model implements PermissionContract
{
    use IsPermission;
}
