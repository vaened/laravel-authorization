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

/**
 * Class Helpers.
 *
 * @package Vaened\Authorization\Facades
 *
 * @method static \Vaened\Authorization\Contracts\Authorizable|\Illuminate\Database\Eloquent\Model authenticated(?string $guard = null)
 * @method static \Vaened\Authorization\Authorizer authorizer()
 * @method static \Illuminate\Support\Collection except(\Illuminate\Support\Collection $grantableCollection, array $exceptNames)
 *
 * @see \Vaened\Authorization\Support\Helper
 */
class Helper extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'authorization.helpers';
    }
}
