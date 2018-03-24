<?php
/**
 * Created on 20/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Drivers\Redis;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Drivers\Redis\Sessions\Formatter;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
//
//class TestTest extends TestCase
//{
//    public function test_test()
//    {
//        $authorizable = $this->user();
//        $p2 = $this->permissions(3);
//        $p = $this->permission(['secret_name' => 'text,ext']);
//        $r = $this->role(['secret_name' => 'creator']);
//        $r2 = $this->role(['secret_name' => 'editor']);
//        $r->syncGrant($this->permissions(3)->all());
//        $r->grant($p);
//        $r2->syncGrant($this->permissions(3)->all());
//        $r2->grant($p);
//
//        $authorizable->syncGrant([$r, $r2]);
//        $authorizable->syncGrant($p2->all());
//
//        $permissions = $this->extractPermissionsKeys($authorizable->getPermissionModels());
//
//        $rolePermissions = $authorizable->getRoleModels()->map(function (RoleContract $role) {
//            return $this->extractPermissionsKeys($role->getPermissionModels());
//        })->collapse()->merge($permissions)->unique();
//
//        $formatter = new Formatter($authorizable);
//        //
//        //dd($formatter->getPermissions(), $formatter->getRoles());
//        //dd($rolePermissions, $authorizable->getRoleModels()->map($this->extractKeys()));
//    }
//
//    private function extractPermissionsKeys(EloquentCollection $permissions): Collection
//    {
//        return $permissions->map($this->extractKeys());
//    }
//
//    private function extractKeys(): Closure
//    {
//        return function (Grantable $grantable): string {
//            return $grantable->getIdentificationKey();
//        };
//    }
//}
