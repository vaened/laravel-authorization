<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Models;

use Closure;
use Vaened\Authorization\Contracts\Grantable;

trait OwnerTest
{
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
