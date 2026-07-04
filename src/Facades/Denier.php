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

use Vaened\Authorization\Contracts\PermissionsOwner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Denier.
 *
 * @package Vaened\Authorization\Facades
 * @method static void permissions(PermissionsOwner $owner, Collection $permissions)
 */
class Denier extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Vaened\Authorization\Operators\Denier::class;
    }
}
