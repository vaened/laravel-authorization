<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Drivers\Cache;

use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Contracts\PermissionsOwner;
use Enea\Authorization\Contracts\RolesOwner;
use Enea\Authorization\Drivers\Cache\KeyBuilder;
use Enea\Authorization\Drivers\Cache\Manager;
use Enea\Authorization\Drivers\Cache\Repositories\PermissionRepository;
use Enea\Authorization\Drivers\Cache\Repositories\RoleRepository;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Cache\Repository as CacheContract;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mockery;
use Mockery\MockInterface;

class ManagerTest extends TestCase
{
    public function test_the_permission_cache_works_correctly(): void
    {
        $mock = Mockery::mock(PermissionsOwner::class);
        $mock->shouldReceive('getIdentificationKey')->times(4);
        $mock->shouldReceive('getPermissionModels')->once()->andReturn(new EloquentCollection());
        $this->manager()->permissions($mock);
        $this->verifyCache($mock, PermissionRepository::getSuffix());
    }

    public function test_the_roles_cache_works_correctly(): void
    {
        $mock = Mockery::mock(RolesOwner::class);
        $mock->shouldReceive('getIdentificationKey')->times(4);
        $mock->shouldReceive('getRoleModels')->once()->andReturn(new EloquentCollection());
        $this->manager()->roles($mock);
        $this->verifyCache($mock, RoleRepository::getSuffix());
    }

    private function verifyCache(MockInterface $mock, string $suffix): void
    {
        $this->assertTrue($this->hasCacheValue($mock, $suffix));
        $this->manager()->forget($mock);
        $this->assertFalse($this->hasCacheValue($mock, $suffix));
    }

    private function manager(): Manager
    {
        return $this->app->make(Manager::class);
    }

    private function hasCacheValue(GrantableOwner $owner, string $suffix): bool
    {
        $key = new KeyBuilder();
        return $this->app->make(CacheContract::class)->has("{$key->make($owner)}.{$suffix}");
    }
}
