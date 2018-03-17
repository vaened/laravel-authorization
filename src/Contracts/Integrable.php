<?php
/**
 * Created on 02/03/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Integrable
{
    public function isMemberOf(string $role): bool;

    public function isntMemberOf(string $role): bool;
}
