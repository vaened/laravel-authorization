<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Contracts;

/**
 * Interface PermissionContract.
 *
 * @package Enea\Authorization\Contracts
 *
 * @property \Enea\Authorization\Models\UserPermission pivot
 */
interface PermissionContract extends Grantable
{
    public static function locateByName(string $secretName): ?PermissionContract;
}
