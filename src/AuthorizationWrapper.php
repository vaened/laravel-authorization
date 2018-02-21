<?php
/**
 * Created on 14/02/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Support\IsJsonable;
use Enea\Authorization\Support\Serializable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as SupportCollection;

class AuthorizationWrapper implements Serializable
{
    use IsJsonable;

    private $roles;

    private $permissions;

    public function __construct(EloquentCollection $roles, EloquentCollection $permissions)
    {
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    public static function fill(EloquentCollection $roles, EloquentCollection $permissions): AuthorizationWrapper
    {
        return new static($roles, $permissions);
    }

    public function getPermissions(): EloquentCollection
    {
        return $this->permissions;
    }

    public function getRoles(): EloquentCollection
    {
        return $this->roles;
    }

    public function getRolesAndPermissions(): SupportCollection
    {
        return collect()->merge($this->getPermissions())->merge($this->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->getRolesAndPermissions()->toArray();
    }
}
