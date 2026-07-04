<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Drivers\Database;

use Vaened\Authorization\Contracts\RolesOwner;

class RoleEvaluator extends Evaluator
{
    public function evaluate(RolesOwner $owner, array $roles): bool
    {
        return $this->has($owner->roles()->getQuery())($roles);
    }
}
