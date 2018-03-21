<?php
/**
 * Created on 20/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Closure;
use Enea\Authorization\Contracts\Grantable;

trait OwnerTest
{
    use Assertable;

    protected function equalsAuthorization(Grantable $grantable): Closure
    {
        return function (Grantable $granted) use ($grantable) : bool {
            return count(array_filter([
                    (string) $granted->getIdentificationKey() === $grantable->getIdentificationKey(),
                    $granted->getSecretName() === $grantable->getSecretName(),
                ])) === 2;
        };
    }
}
