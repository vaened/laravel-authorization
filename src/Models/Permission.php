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

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Vaened\Authorization\Configuration\Tables;
use Vaened\Sentinel\Permission as PermissionContract;
use Vaened\Sentinel\SubjectPermission as SubjectPermissionContract;
use Vaened\Sentinel\SubjectPermissionState;

class Permission extends Authorization implements PermissionContract, SubjectPermissionContract
{
    protected function tableName(): string
    {
        return Tables::permissions();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            Tables::rolePermissions(),
            'permission_id',
            'role_id',
        );
    }

    public function state(): SubjectPermissionState
    {
        $pivot = $this->getRelationValue('pivot');

        if (!$pivot instanceof MorphPivot) {
            return SubjectPermissionState::Direct;
        }

        return SubjectPermissionState::fromBoolean((bool) $pivot->getAttributeValue('denied'));
    }
}
