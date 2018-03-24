<?php

declare(strict_types=1);

/**
 * Created on 18/02/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

class AuthorizationNotGrantedException extends UncompletedOperationException
{
    protected function getOperationName(): string
    {
        return 'granted';
    }
}
