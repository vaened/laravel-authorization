<?php
declare(strict_types=1);

/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

class AuthorizationNotRevokedException extends UncompletedOperationException
{
    protected function getOperationName(): string
    {
        return 'revoked';
    }
}
