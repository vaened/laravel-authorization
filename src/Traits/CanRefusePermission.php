<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

trait CanRefusePermission
{
    /**
     * {@inheritdoc}
     */
    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }
}
