<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Permissible
{
    /**
     * Returns true in case the user has permission passed by parameter.
     *
     * @param string $permission
     * @return bool
     */
    public function can(string $permission): bool;

    /**
     * Returns true in case of not being authorized.
     *
     * @param string $permission
     * @return bool
     */
    public function cannot(string $permission): bool;
}
