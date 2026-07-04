<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Facades;

use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Granter.
 *
 * @package Vaened\Authorization\Facades
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * @method static void permissions(PermissionsOwner $owner, Collection $permissions)
 * @method static void roles(RolesOwner $owner, Collection $roles)
 */
class Granter extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Vaened\Authorization\Operators\Granter::class;
    }
}
