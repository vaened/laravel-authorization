<?php
/**
 * Created on 15/02/18 by enea dhack.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Contracts\Grantable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Revoker.
 *
 * @package Enea\Authorization\Facades
 * @author enea dhack <enea.so@live.com>
 *
 * @method static bool revoke(Authorizable $user, Grantable $grantable)
 * @method static void syncRevoke(Authorizable $user, Collection $grantableCollection)
 */
class Revoker extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Enea\Authorization\Operators\Revoker::class;
    }
}
