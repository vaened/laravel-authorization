<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Sentinel\Role as RoleContract;

class Role extends Authorization implements RoleContract
{
    protected function tableName(): string
    {
        return Tables::roles();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            Tables::rolePermissions(),
            'role_id',
            'permission_id',
        );
    }
}
