<?php

declare(strict_types=1);

/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

class UnsupportedDriverException extends AuthorizationException
{
    public function __construct(string $driver)
    {
        parent::__construct("the '{$driver}' driver is not supported");
    }
}
