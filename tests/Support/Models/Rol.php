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

use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Traits\IsRole;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model implements RoleContract
{
    use IsRole;
}
