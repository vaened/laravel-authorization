<?php
/**
 * Created on 12/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;

interface Operation
{
    public function getGrantable(): Grantable;

    public function getOwner(): GrantableOwner;
}
