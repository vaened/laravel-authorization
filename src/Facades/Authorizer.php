<?php
/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};
use Illuminate\Support\Facades\Facade;

/**
 * Class Authorizer.
 *
 * @package Enea\Authorization\Facades
 * @author enea dhack <enea.so@live.com>
 *
 * @method static bool can(PermissionsOwner $owner, string $permission)
 * @method static bool syncCan(PermissionsOwner $owner, array $permissions)
 * @method static bool is(RolesOwner $owner, string $role)
 * @method static bool syncIs(RolesOwner $owner, array $roles)
 */
class Authorizer extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Enea\Authorization\Authorizer::class;
    }
}
