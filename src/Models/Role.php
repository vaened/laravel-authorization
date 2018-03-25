<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Traits\IsRole;

class Role extends Grantable implements RoleContract
{
    use IsRole;

    protected function getConfigTableName(): string
    {
        return Config::roleTableName();
    }
}
