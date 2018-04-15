<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Operators;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Events\Denied;
use Enea\Authorization\Exceptions\AuthorizationNotDeniedException;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;

class Denier
{
    use WithDeniablePermission;

    protected const DENIED = true;

    private $event;

    public function __construct(Dispatcher $event)
    {
        $this->event = $event;
    }

    public function permissions(PermissionsOwner $owner, Collection $permissions): void
    {
        $granted = $this->getPermissions($owner, $permissions);
        $granted->each($this->denialStatus($owner, self::DENIED));
        $this->except($granted, $permissions)->each($this->deny($owner));
        $this->event->dispatch(new Denied($owner, $permissions));
    }

    private function deny(PermissionsOwner $owner): Closure
    {
        return function (PermissionContract $permission) use ($owner): void {
            try {
                $owner->permissions()->save($permission, ['denied' => self::DENIED]);
            } catch (Exception $exception) {
                throw new AuthorizationNotDeniedException($permission, $exception);
            }
        };
    }

    protected function throwException(Grantable $grantable): void
    {
        throw new AuthorizationNotDeniedException($grantable);
    }

    protected function isModifiable(PermissionContract $permission): bool
    {
        return ! $permission->pivot->isDenied();
    }
}
