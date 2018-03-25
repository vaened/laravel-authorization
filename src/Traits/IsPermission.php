<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\PermissionContract;

trait IsPermission
{
    use Grantable;

    public static function locateByName(string $secretName): ?PermissionContract
    {
        $permission = static::query()->where('secret_name', $secretName)->first();
        return $permission instanceof PermissionContract ? $permission : null;
    }
}
