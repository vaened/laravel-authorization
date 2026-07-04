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

use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Support\Config;
use Vaened\Authorization\Traits\IsPermission;

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
