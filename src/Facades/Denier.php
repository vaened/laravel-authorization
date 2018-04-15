<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\PermissionsOwner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Denier.
 *
 * @package Enea\Authorization\Facades
 * @method static void permissions(PermissionsOwner $owner, Collection $permissions)
 */
class Denier extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Enea\Authorization\Operators\Denier::class;
    }
}
