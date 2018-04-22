<?php

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
 * Class Helpers.
 *
 * @package Enea\Authorization\Facades
 *
 * @method static \Enea\Authorization\Contracts\Authorizable|\Illuminate\Database\Eloquent\Model authenticated(?string $guard = null)
 * @method static \Enea\Authorization\Authorizer authorizer()
 * @method static \Illuminate\Support\Collection except(\Illuminate\Support\Collection $grantableCollection, array $exceptNames)
 *
 * @see \Enea\Authorization\Support\Helper
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
