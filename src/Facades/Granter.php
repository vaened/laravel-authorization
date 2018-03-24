<?php
declare(strict_types=1);

/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Granter.
 *
 * @package Enea\Authorization\Facades
 * @author enea dhack <enea.so@live.com>
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
        return \Enea\Authorization\Operators\Granter::class;
    }
}
