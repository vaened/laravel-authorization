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
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;
use Vaened\Sentinel\Permission as PermissionContract;
use Vaened\Sentinel\Role as RoleContract;
use Vaened\Sentinel\Subject;

class Role extends Authorization implements RoleContract
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            Tables::rolePermissions(),
            'role_id',
            'permission_id',
        );
    }

    public function scope(): Subject|null
    {
        $scope = $this->context()->getResults();

        return $scope instanceof Subject ? $scope : null;
    }

    public function context(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'scope_type', 'scope_id');
    }

    public function grant(PermissionContract ...$permissions): void
    {
        Granter::grant($this, ...$permissions);
    }

    public function revoke(PermissionContract ...$permissions): void
    {
        Revoker::revoke($this, ...$permissions);
    }

    protected function tableName(): string
    {
        return Tables::roles();
    }
}
