<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\PermissionContract;

trait HasPermission
{
    use Grantable;

    public static function locateByName(string $secretName): ? PermissionContract
    {
        return static::grantableBySecretName($secretName);
    }
}
