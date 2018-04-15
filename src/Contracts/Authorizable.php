<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Contracts;

interface Authorizable extends RolesOwner, PermissionsOwner, Integrable, Permissible
{
    public function grant(Grantable $grantable): void;

    public function grantMultiple(array $grantables): void;

    public function deny(PermissionContract $permission): void;

    public function denyMultiple(array $permissions): void;

    public function revoke(Grantable $grantable): void;

    public function revokeMultiple(array $grantables): void;
}
