<?php
/**
 * Created by enea dhack - 01/08/17 12:43 AM.
 */

namespace Enea\Authorization\Test\Support\Traits;

use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;
use Enea\Authorization\Test\Support\Models\User;

trait Factories
{
    protected function permission(array $customAttributes = array()): Permission
    {
        return factory(Permission::class)->create($customAttributes);
    }

    protected function role(array $customAttributes = array()): Role
    {
        return factory(Role::class)->create($customAttributes);
    }

    protected function user(): User
    {
        return factory(User::class)->create();
    }
}
