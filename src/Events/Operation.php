<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Events;

use Vaened\Authorization\Contracts\Owner;
use Illuminate\Support\Collection;

interface Operation
{
    public function getGrantableCollection(): Collection;

    public function getOwner(): Owner;
}
