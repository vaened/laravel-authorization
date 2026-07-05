<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Traits;

use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Authorizer as AuthorizerContract;
use Vaened\Authorization\Operators\Granter;
use Vaened\Authorization\Operators\Revoker;
use Vaened\Authorization\Support\Config;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait HasRole.
 *
 * @package Vaened\Authorization\Traits
 *
 * @property EloquentCollection permissions
 */
trait IsRole
{
    use Grantable;

    public static function locateByName(string $secretName): ?RoleContract
    {
        $role = static::query()->where('secret_name', $secretName)->first();
        return $role instanceof RoleContract ? $role : null;
    }

    public function can(string $permission): bool
    {
        return app(AuthorizerContract::class)->can($this, $permission);
    }

    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    public function grant(PermissionContract $permission): void
    {
        $this->grantMultiple([$permission]);
    }

    public function grantMultiple(array $permissions): void
    {
        app(Granter::class)->permissions($this, collect($permissions));
    }

    public function revoke(PermissionContract $permission): void
    {
        $this->revokeMultiple([$permission]);
    }

    public function revokeMultiple(array $permissions): void
    {
        app(Revoker::class)->permissions($this, collect($permissions));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Config::permissionModel(), Config::rolePermissionTableName(), 'role_id', 'permission_id');
    }

    public function getPermissionModels(): EloquentCollection
    {
        return $this->permissions;
    }
}
