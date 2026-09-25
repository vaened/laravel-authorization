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
use Vaened\Authorization\Facades\Denier;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;
use Vaened\Sentinel\Authorization as AuthorizationContract;
use Vaened\Sentinel\Identifier;
use Vaened\Sentinel\Permission;

/**
 * Provides authorization assignment operations for an authorizable Eloquent model.
 *
 * @mixin Model&Authorizable
 */
trait Authorize
{
    public function id(): int|string|Identifier
    {
        return $this->getKey();
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
}
