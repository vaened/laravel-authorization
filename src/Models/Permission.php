<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Support\Config;
use Enea\Authorization\Traits\IsPermission;

class Permission extends Grantable implements PermissionContract
{
    use IsPermission;

    protected function getConfigTableName(): string
    {
        return Config::permissionTableName();
    }

    public function getFillable()
    {
        return array_merge(parent::getFillable(), ['denied']);
    }
}
