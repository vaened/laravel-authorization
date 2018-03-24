<?php
declare(strict_types=1);

/**
 * Created on 07/03/18 by enea dhack.
 */

namespace Enea\Authorization\Drivers\Database;

use Enea\Authorization\Authorizer as AuthorizerContract;
use Enea\Authorization\Contracts\{
    PermissionsOwner, RolesOwner
};

class Authorizer implements AuthorizerContract
{
    private $permission;

    private $role;

    public function __construct(PermissionEvaluator $permission, RoleEvaluator $role)
    {
        $this->permission = $permission;
        $this->role = $role;
    }

    public function can(PermissionsOwner $owner, string $permission): bool
    {
        return $this->canAny($owner, [$permission]);
    }

    public function canAny(PermissionsOwner $owner, array $permissions): bool
    {
        return $this->permission->evaluate($owner, $permissions);
    }

    public function is(RolesOwner $owner, string $role): bool
    {
        return $this->isAny($owner, [$role]);
    }

    public function isAny(RolesOwner $owner, array $roles): bool
    {
        return $this->role->evaluate($owner, $roles);
    }
}
