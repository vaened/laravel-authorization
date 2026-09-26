<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Sentinel\Subject;

/**
 * Provides permission and role checks for an authorizable Eloquent model.
 *
 * @mixin Model&Subject
 */
trait Abilities
{
    public function can(string ...$permissions): bool
    {
        return Authorizer::can($this, $permissions);
    }

    public function cannot(string ...$permissions): bool
    {
        return Authorizer::cannot($this, $permissions);
    }

    public function actsAs(string ...$roles): bool
    {
        return Authorizer::is($this, $roles);
    }

    public function actsNotAs(string ...$roles): bool
    {
        return Authorizer::isnt($this, $roles);
    }
}
