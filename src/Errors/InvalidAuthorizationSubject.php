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
use Vaened\Sentinel\Subject;

final class InvalidAuthorizationSubject extends AuthorizationError
{
    public static function forUser(object $user): self
    {
        return new self(sprintf(
            'Authenticated user [%s] must implement [%s] to use the default authorization subject resolver.',
            $user::class,
            Subject::class,
        ));
    }
}
