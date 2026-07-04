<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers;

use Vaened\Authorization\Contracts\Owner;
use Vaened\Authorization\Contracts\PermissionsOwner;
use Vaened\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;

interface ManagerContract
{
    public function permissions(PermissionsOwner $owner): Collection;

    public function roles(RolesOwner $owner): Collection;

    public function forget(Owner $owner): void;
}
