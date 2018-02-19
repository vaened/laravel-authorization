<?php
/**
 * Created on 18/02/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Enea\Authorization\Contracts\Grantable;

class AuthorizationNotGrantedException extends AuthorizationException
{
    public function __construct(Grantable $grantable)
    {
        parent::__construct("The authorization '{$grantable->getSecretName()}' could not be granted");
    }
}
