<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Facades;

use Illuminate\Support\Facades\Facade;
use Vaened\Authorization\Contracts\{PermissionsOwner, RolesOwner};
use Vaened\Authorization\Traits\Authorizable;
use Vaened\Authorization\Traits\isRole;

/**
 * Class Authorizer.
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
        return \Vaened\Authorization\Authorizer::class;
    }
}
