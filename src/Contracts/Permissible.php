<?php
declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Permissible
{
    public function can(string $permission): bool;

    public function cannot(string $permission): bool;
}
