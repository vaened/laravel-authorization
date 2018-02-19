<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

use Enea\Authorization\AuthorizationWrapper;

interface Authorizable extends RolesOwner, PermissionsOwner
{
    public function grant(Grantable $grantable): void;

    public function syncGrant(array $grantables): void;

    public function revoke(Grantable $grantable): void;

    public function syncRevoke(array $grantables): void;

    public function getAuthorizationWrapper(): AuthorizationWrapper;
}
