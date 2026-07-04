<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Cache\Repositories;

use Vaened\Authorization\Contracts\RolesOwner;
use Illuminate\Support\Collection;

class RoleRepository extends Repository
{
    public static function getSuffix(): string
    {
        return 'roles';
    }

    public function toCollection(RolesOwner $owner): Collection
    {
        return $this->remember($owner, function () use ($owner) {
            return $owner->roles()->get()->map($this->parse());
        });
    }
}
