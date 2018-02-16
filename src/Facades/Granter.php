<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Granter
 *
 * @package Enea\Authorization\Facades
 * @author enea dhack <enea.so@live.com>
 *
 * @method static bool grant(Authorizable $user, Grantable $grantable)
 * @method static void syncGrant(Authorizable $user, Collection $grantableCollection)
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
