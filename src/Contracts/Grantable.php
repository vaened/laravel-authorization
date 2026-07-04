<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

interface Grantable extends Identifiable
{
    public function getSecretName(): string;
}
