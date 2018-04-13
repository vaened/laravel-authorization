<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Drivers\Cache;

use Enea\Authorization\Contracts\Owner;
use Enea\Authorization\Drivers\Cache\KeyBuilder;
use Enea\Authorization\Drivers\Cache\Manager;
use Enea\Authorization\Drivers\Cache\Repositories\PermissionRepository;
use Enea\Authorization\Drivers\Cache\Repositories\RoleRepository;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Cache\Repository as CacheContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection as EloquentCollection;
use Mockery;
use Mockery\MockInterface;

class ManagerTest extends TestCase
{
    private $relationship;

    public function setUp()
    {
        parent::setUp();
        $this->relationship = Mockery::mock(BelongsToMany::class);
        $this->relationship->shouldReceive('get')->andReturn(new EloquentCollection());
    }

    public function test_the_permission_cache_works_correctly(): void
    {
        $mock = Mockery::mock('Enea\Authorization\Contracts\PermissionsOwner[permissions]')->shouldIgnoreMissing();
        $mock->shouldReceive('permissions')->twice()->andReturn($this->relationship);
        $this->manager()->permissions($mock);
        $this->manager()->permissions($mock);
        $this->verifyCache($mock, PermissionRepository::getSuffix());
        $this->manager()->permissions($mock);
    }

    public function test_the_roles_cache_works_correctly(): void
    {
        $mock = Mockery::mock('Enea\Authorization\Contracts\RolesOwner[roles]')->shouldIgnoreMissing();
        $mock->shouldReceive('roles')->twice()->andReturn($this->relationship);
        $this->manager()->roles($mock);
        $this->manager()->roles($mock);
        $this->verifyCache($mock, RoleRepository::getSuffix());
        $this->manager()->roles($mock);
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

    private function hasCacheValue(Owner $owner, string $suffix): bool
    {
        $key = new KeyBuilder();
        return $this->app->make(CacheContract::class)->has("{$key->make($owner)}.{$suffix}");
    }
}
