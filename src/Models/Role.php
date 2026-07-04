<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Models;

use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Support\Config;
use Vaened\Authorization\Traits\IsRole;

class Role extends Grantable implements RoleContract
{
    use IsRole;

    protected function getConfigTableName(): string
    {
        return Config::roleTableName();
    }
}
