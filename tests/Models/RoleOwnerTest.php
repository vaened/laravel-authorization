<?php

declare(strict_types=1);

/**
 * Created on 20/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Closure;
use Enea\Authorization\Contracts\Integrable;
use Enea\Authorization\Contracts\RoleContract;

trait RoleOwnerTest
{
    protected function is(Integrable $owner): Closure
    {
        return function (RoleContract $role) use ($owner): void {
            $this->assertTrue($owner->isMemberOf($role->getSecretName()));
        };
    }

    protected function isnt(Integrable $owner): Closure
    {
        return function (RoleContract $role) use ($owner): void {
            $this->assertTrue($owner->isntMemberOf($role->getSecretName()));
        };
    }
}
