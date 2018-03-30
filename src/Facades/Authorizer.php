<?php

declare(strict_types=1);

/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization\Facades;

use Enea\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};
use Enea\Authorization\Traits\Authorizable;
use Enea\Authorization\Traits\isRole;
use Illuminate\Support\Facades\Facade;

/**
 * Class Authorizer.
 *
 * @package Enea\Authorization\Facades
 * @author enea dhack <enea.so@live.com>
 *
 * @method static bool can(PermissionsOwner | Authorizable | isRole $owner, string $permission)
 * @method static bool canAny(PermissionsOwner | Authorizable | isRole $owner, array $permissions)
 * @method static bool is(RolesOwner | Authorizable $owner, string $role)
 * @method static bool isAny(RolesOwner | Authorizable $owner, array $roles)
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
