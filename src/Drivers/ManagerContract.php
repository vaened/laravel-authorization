<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers;

use Enea\Authorization\Contracts\Owner;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;

interface ManagerContract
{
    public function permissions(PermissionsOwner $owner): Collection;

    public function roles(RolesOwner $owner): Collection;

    public function forget(Owner $owner): void;
}
