<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Database;

use Vaened\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};
use Vaened\Authorization\Drivers\Authorizer as BaseAuthorizer;

class Authorizer extends BaseAuthorizer
{
    private $permission;

    private $role;

    public function __construct(PermissionEvaluator $permission, RoleEvaluator $role)
    {
        $this->permission = $permission;
        $this->role = $role;
    }

    public function canAny(PermissionsOwner $owner, array $permissions): bool
    {
        return $this->permission->evaluate($owner, $permissions);
    }

    public function isAny(RolesOwner $owner, array $roles): bool
    {
        return $this->role->evaluate($owner, $roles);
    }
}
