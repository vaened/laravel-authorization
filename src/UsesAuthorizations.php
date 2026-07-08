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
use Vaened\Authorization\Facades\Denier;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;
use Vaened\Sentinel\Authorization as AuthorizationContract;
use Vaened\Sentinel\Identifier;
use Vaened\Sentinel\Permission;

/**
 * Provides authorization checks for an authorizable Eloquent model.
 *
 * @mixin Model&Authorizable
 */
trait UsesAuthorizations
{
    public function id(): int|string|Identifier
    {
        return $this->getKey();
    }

    public function can(string ...$permissions): bool
    {
        return Authorizer::can($this, $permissions);
    }

    public function cannot(string ...$permissions): bool
    {
        return Authorizer::cannot($this, $permissions);
    }

    public function grant(AuthorizationContract ...$authorizations): void
    {
        Granter::grant($this, ...$authorizations);
    }

    public function deny(Permission ...$permissions): void
    {
        Denier::deny($this, ...$permissions);
    }

    public function revoke(AuthorizationContract ...$authorizations): void
    {
        Revoker::revoke($this, ...$authorizations);
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
