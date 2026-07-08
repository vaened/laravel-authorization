<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Models\Permission;
use Vaened\Authorization\Models\Role;

trait Authorizable
{
    public function id(): int|string
    {
        return $this->getKey();
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'authorizable',
            Tables::subjectRoles(),
            'authorizable_id',
            'role_id',
        );
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            Permission::class,
            'authorizable',
            Tables::subjectPermissions(),
            'authorizable_id',
            'permission_id',
        )->withPivot('denied');
    }
}
