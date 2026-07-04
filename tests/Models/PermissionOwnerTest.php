<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Models;

use Closure;
use Vaened\Authorization\Contracts\Permissible;
use Vaened\Authorization\Contracts\PermissionContract;

trait PermissionOwnerTest
{
    protected function cannot(Permissible $owner): Closure
    {
        return function (PermissionContract $permission) use ($owner): void {
            $this->assertTrue($owner->cannot($permission->getSecretName()));
        };
    }

    protected function can(Permissible $owner): Closure
    {
        return function (PermissionContract $permission) use ($owner): void {
            $this->assertTrue($owner->can($permission->getSecretName()));
        };
    }
}
