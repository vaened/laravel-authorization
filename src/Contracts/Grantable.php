<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Grantable
{
    public function getSecretName(): string;
}
