<?php
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
        return $this->permission->evaluate($owner, $permission);
    }

    public function syncCan(PermissionsOwner $owner, array $permissions): bool
    {
        return $this->permission->syncEvaluate($owner, $permissions);
    }

    public function is(RolesOwner $owner, string $role): bool
    {
        return $this->role->evaluate($owner, $role);
    }

    public function syncIs(RolesOwner $owner, array $roles): bool
    {
        return $this->role->syncEvaluate($owner, $roles);
    }
}