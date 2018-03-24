<?php

declare(strict_types=1);

/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UnauthorizedException extends AccessDeniedHttpException implements AuthorizationException
{
    public function __construct(?string $message = null, array $headers = array())
    {
        parent::__construct($message, null, 0, $headers);
    }
}
