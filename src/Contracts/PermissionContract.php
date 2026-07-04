<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Contracts;

/**
 * Interface PermissionContract.
 *
 * @package Vaened\Authorization\Contracts
 *
 * @property \Vaened\Authorization\Models\UserPermission pivot
 */
interface PermissionContract extends Grantable
{
    public static function locateByName(string $secretName): ?PermissionContract;
}
