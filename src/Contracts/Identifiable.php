<?php
declare(strict_types=1);

/**
 * Created on 04/03/18 by enea dhack.
 */

namespace Enea\Authorization\Contracts;

interface Identifiable
{
    public function getIdentificationKey(): string;
}
