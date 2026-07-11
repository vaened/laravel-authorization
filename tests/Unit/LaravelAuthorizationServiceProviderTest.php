<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Unit;

use Vaened\Authorization\LaravelAuthorizationServiceProvider;
use Vaened\Authorization\Tests\TestCase;
use Vaened\Sentinel\Authorization\Authorizer;
use Vaened\Sentinel\Authorization\PermissionEntryProvider;
use Vaened\Sentinel\Authorization\RoleEntryProvider;
use Vaened\Sentinel\Cache\CachedPermissionRepository;
use Vaened\Sentinel\Cache\CachedRolePermissionRepository;
use Vaened\Sentinel\Cache\CachedRoleRepository;
use Vaened\Sentinel\Cache\CachedSubjectPermissionRepository;
use Vaened\Sentinel\Cache\CachedSubjectRoleRepository;
use Vaened\Sentinel\Operators\Denier;
use Vaened\Sentinel\Operators\Granter;
use Vaened\Sentinel\Operators\Revoker;
use Vaened\Sentinel\Registry\PermissionRegistry;
use Vaened\Sentinel\Registry\RoleRegistry;
use Vaened\Sentinel\Repositories\PermissionRepository;
use Vaened\Sentinel\Repositories\RolePermissionRepository;
use Vaened\Sentinel\Repositories\RoleRepository;
use Vaened\Sentinel\Repositories\SubjectPermissionRepository;
use Vaened\Sentinel\Repositories\SubjectRoleRepository;

final class LaravelAuthorizationServiceProviderTest extends TestCase
{
    public function test_it_registers_the_expected_repositories(): void
    {
        self::assertInstanceOf(CachedRoleRepository::class, $this->app->make(RoleRepository::class));
        self::assertInstanceOf(CachedPermissionRepository::class, $this->app->make(PermissionRepository::class));
        self::assertInstanceOf(CachedRolePermissionRepository::class, $this->app->make(RolePermissionRepository::class));
        self::assertInstanceOf(CachedSubjectRoleRepository::class, $this->app->make(SubjectRoleRepository::class));
        self::assertInstanceOf(CachedSubjectPermissionRepository::class, $this->app->make(SubjectPermissionRepository::class));
    }

    public function test_it_registers_the_core_services(): void
    {
        self::assertInstanceOf(PermissionEntryProvider::class, $this->app->make(PermissionEntryProvider::class));
        self::assertInstanceOf(RoleEntryProvider::class, $this->app->make(RoleEntryProvider::class));
        self::assertInstanceOf(Authorizer::class, $this->app->make(Authorizer::class));
        self::assertInstanceOf(Granter::class, $this->app->make(Granter::class));
        self::assertInstanceOf(Denier::class, $this->app->make(Denier::class));
        self::assertInstanceOf(Revoker::class, $this->app->make(Revoker::class));
        self::assertInstanceOf(RoleRegistry::class, $this->app->make(RoleRegistry::class));
        self::assertInstanceOf(PermissionRegistry::class, $this->app->make(PermissionRegistry::class));
    }

    public function test_it_merges_the_authorization_configuration(): void
    {
        self::assertSame('roles', config('authorization.tables.roles'));
        self::assertSame('permissions', config('authorization.tables.permissions'));
        self::assertSame('role_permissions', config('authorization.tables.role_permissions'));
        self::assertSame('subject_roles', config('authorization.tables.subject_roles'));
        self::assertSame('subject_permissions', config('authorization.tables.subject_permissions'));
        self::assertSame('authorization.permissions', config('authorization.middlewares.permissions'));
        self::assertSame('authorization.roles', config('authorization.middlewares.roles'));
        self::assertNull(config('authorization.gate'));
        self::assertNull(config('authorization.cache.store'));
        self::assertSame('authorization', config('authorization.cache.prefix'));
        self::assertNull(config('authorization.cache.ttl'));
    }

    public function test_it_is_the_registered_package_provider(): void
    {
        self::assertContains(LaravelAuthorizationServiceProvider::class, $this->getPackageProviders($this->app));
    }

    public function test_it_registers_the_cache_invalidation_command(): void
    {
        $this->artisan('authorization:cache:invalidate')
             ->expectsOutput('Authorization cache invalidated.')
             ->assertExitCode(0);
    }
}
