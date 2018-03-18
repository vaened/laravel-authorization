<?php
/**
 * Created by enea dhack - 01/08/17 12:43 AM.
 */

namespace Enea\Authorization\Tests\Support\Traits;

use Enea\Authorization\Models\Permission;
use Enea\Authorization\Models\Role;
use Enea\Authorization\Tests\Support\Models\User;
use Illuminate\Support\Collection;

trait Factories
{
    protected function permission(array $customAttributes = array()): Permission
    {
        return factory(Permission::class)->create($customAttributes);
    }

    protected function permissions(int $amount = 1): Collection
    {
        return factory(Permission::class, $amount)->create();
    }

    protected function role(array $customAttributes = array()): Role
    {
        return factory(Role::class)->create($customAttributes);
    }

    protected function roles(int $amount = 1): Collection
    {
        return factory(Role::class, $amount)->create();
    }

    protected function user(): User
    {
        return factory(User::class)->create();
    }
}
