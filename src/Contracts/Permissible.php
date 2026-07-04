<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

interface Permissible
{
    public function can(string $permission): bool;

    public function cannot(string $permission): bool;
}
