<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Exceptions;

class AuthorizationNotRevokedException extends UncompletedOperationException
{
    protected function getOperationName(): string
    {
        return 'revoked';
    }
}
