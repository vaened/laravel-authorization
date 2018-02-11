<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Grantable
{
    /**
     * Return the secret name of grantable.
     *
     * @return string
     */
    public function getSecretName(): string;
}
