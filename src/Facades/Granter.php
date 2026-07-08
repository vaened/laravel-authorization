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
use Vaened\Sentinel\Authorization;
use Vaened\Sentinel\Operators\Granter as GranterService;
use Vaened\Sentinel\Role;
use Vaened\Sentinel\Subject;

/**
 * @method static void grant(Subject|Role $owner, Authorization ...$authorizations)
 *
 * @see GranterService
 */
final class Granter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GranterService::class;
    }
}
