<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Repositories;

use Enea\Authorization\Contracts\PermissionContract;

class PermissionRepository extends Repository
{
    public function create(string $name, ?string $description = null): PermissionContract
    {
        return $this->register([Struct::create($name, $description)])->first();
    }

    protected function contract(): string
    {
        return PermissionContract::class;
    }
}
