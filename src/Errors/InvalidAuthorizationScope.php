<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Errors;

use Vaened\Sentinel\Errors\AuthorizationError;
use Vaened\Sentinel\Role;
use Vaened\Sentinel\Subject;

final class InvalidAuthorizationScope extends AuthorizationError
{
    public static function forRole(Role $role, object $scope): InvalidAuthorizationScope
    {
        return new InvalidAuthorizationScope(sprintf(
            'Role [%s] has an invalid authorization scope [%s]. Expected an instance of [%s].',
            $role->code(),
            $scope::class,
            Subject::class,
        ));
    }
}
