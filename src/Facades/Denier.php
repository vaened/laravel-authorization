<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Facades;

use Illuminate\Support\Facades\Facade;
use Vaened\Sentinel\Operators\Denier as DenierService;
use Vaened\Sentinel\Permission;
use Vaened\Sentinel\Subject;

/**
 * @method static void deny(Subject $owner, Permission ...$permissions)
 *
 * @see DenierService
 */
final class Denier extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DenierService::class;
    }
}
