<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

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

/**
 * Class Authenticated.
 *
 * @package Vaened\Authorization\Facades
 * @method static void can(string... $permissions)
 * @method static void is(string... $roles)
 */
class Authenticated extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Vaened\Authorization\Support\Authenticated::class;
    }
}
