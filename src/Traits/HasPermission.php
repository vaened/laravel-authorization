<?php
declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\PermissionContract;

trait HasPermission
{
    use Grantable;

    public static function locateByName(string $secretName): ?PermissionContract
    {
        $permission = static::query()->where('secret_name', $secretName)->first();
        return $permission instanceof PermissionContract ? $permission : null;
    }
}
