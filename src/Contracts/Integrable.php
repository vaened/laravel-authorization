<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Contracts;

interface Integrable
{
    public function isMemberOf(string $role): bool;

    public function isntMemberOf(string $role): bool;
}
