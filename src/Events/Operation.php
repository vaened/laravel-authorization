<?php

declare(strict_types=1);

/**
 * Created on 12/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\GrantableOwner;
use Illuminate\Support\Collection;

interface Operation
{
    public function getGrantableCollection(): Collection;

    public function getOwner(): GrantableOwner;
}
