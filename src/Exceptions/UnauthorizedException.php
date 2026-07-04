<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UnauthorizedException extends AccessDeniedHttpException implements AuthorizationException
{
    public function __construct(?string $message = null, array $headers = array())
    {
        parent::__construct($message, null, 0, $headers);
    }
}
