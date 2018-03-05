<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Grantable extends Identifiable
{
    public function getSecretName(): string;
}
