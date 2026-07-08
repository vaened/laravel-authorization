<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Illuminate\Support\ServiceProvider;
use Vaened\Authorization\Configuration\Middlewares;
use Vaened\Authorization\Middlewares\AuthorizePermissions;
use Vaened\Authorization\Middlewares\AuthorizeRoles;
use Vaened\Authorization\Persistence\Database\EloquentPermissionRepository;
use Vaened\Authorization\Persistence\Database\EloquentRolePermissionRepository;
use Vaened\Authorization\Persistence\Database\EloquentRoleRepository;
use Vaened\Authorization\Persistence\Database\EloquentSubjectPermissionRepository;
use Vaened\Authorization\Persistence\Database\EloquentSubjectRoleRepository;
use Vaened\Sentinel\Authorization\Authorizer;
use Vaened\Sentinel\Authorization\PermissionEntryProvider;
use Vaened\Sentinel\Authorization\RoleEntryProvider;
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

final class LaravelAuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/authorization.php', 'authorization');

        $this->app->singleton(RoleRepository::class, EloquentRoleRepository::class);
        $this->app->singleton(PermissionRepository::class, EloquentPermissionRepository::class);
        $this->app->singleton(RolePermissionRepository::class, EloquentRolePermissionRepository::class);
        $this->app->singleton(SubjectRoleRepository::class, EloquentSubjectRoleRepository::class);
        $this->app->singleton(SubjectPermissionRepository::class, EloquentSubjectPermissionRepository::class);

        $this->app->singleton(PermissionEntryProvider::class);
        $this->app->singleton(RoleEntryProvider::class);
        $this->app->singleton(Authorizer::class);

        $this->app->singleton(Granter::class);
        $this->app->singleton(Denier::class);
        $this->app->singleton(Revoker::class);

        $this->app->singleton(RoleRegistry::class);
        $this->app->singleton(PermissionRegistry::class);
    }

    public function boot(): void
    {
        $this->app['router']->aliasMiddleware(Middlewares::permissions(), AuthorizePermissions::class);
        $this->app['router']->aliasMiddleware(Middlewares::roles(), AuthorizeRoles::class);

        $this->publishes([
            __DIR__ . '/../config/authorization.php' => config_path('authorization.php'),
        ], 'laravel-authorization-config');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'laravel-authorization-migrations');
    }
}
