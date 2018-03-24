<?php

declare(strict_types=1);

/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Enea\Authorization\Contracts\Grantable;
use RuntimeException;

class GrantableIsNotValidModelException extends RuntimeException implements AuthorizationException
{
    public function __construct(Grantable $grantable)
    {
        parent::__construct("The grantable '{$grantable->getSecretName()}' element is not a valid model ");
    }

    public static function make(Grantable $grantable): GrantableIsNotValidModelException
    {
        return new static($grantable);
    }
}
