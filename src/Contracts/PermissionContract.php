<?php
declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface PermissionContract extends Grantable
{
    public static function locateByName(string $secretName): ?PermissionContract;
}
