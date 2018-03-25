<?php

declare(strict_types=1);

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
    protected function permission(string $name = null): Permission
    {
        return factory(Permission::class)->create($name ? [
            'secret_name' => $name,
            'display_name' => $name,
        ] : []);
    }

    protected function permissions(int $amount = 1): Collection
    {
        return factory(Permission::class, $amount)->create();
    }

    protected function role(string $name = null): Role
    {
        return factory(Role::class)->create($name ? [
            'secret_name' => $name,
            'display_name' => $name,
        ] : []);
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
