<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Exceptions;

class AuthorizationNotDeniedException extends UncompletedOperationException
{
    protected function getOperationName(): string
    {
        return 'denied';
    }
}
