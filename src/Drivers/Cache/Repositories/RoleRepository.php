<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers\Cache\Repositories;

use Enea\Authorization\Contracts\RolesOwner;
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
            return $owner->getRoleModels()->map($this->parse());
        });
    }
}
