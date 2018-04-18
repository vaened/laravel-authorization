<?php
/**
 * Created by enea dhack - 18/04/2018 11:54.
 */

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Authenticated.
 *
 * @package Enea\Authorization\Facades
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
        return \Enea\Authorization\Support\Authenticated::class;
    }
}
