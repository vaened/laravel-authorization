<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Exceptions;

use RuntimeException;

class UnsupportedDriverException extends RuntimeException implements AuthorizationException
{
    public function __construct(string $driver)
    {
        parent::__construct("the '{$driver}' driver is not supported");
    }
}
