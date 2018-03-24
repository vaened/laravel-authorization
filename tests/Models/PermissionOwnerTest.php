<?php
declare(strict_types=1);

/**
 * Created on 20/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Closure;
use Enea\Authorization\Contracts\Permissible;
use Enea\Authorization\Contracts\PermissionContract;

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
