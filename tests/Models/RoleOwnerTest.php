<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Models;

use Closure;
use Vaened\Authorization\Contracts\Integrable;
use Vaened\Authorization\Contracts\RoleContract;

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
