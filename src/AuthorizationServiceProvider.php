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

use Vaened\Authorization\Commands\InstallCommand;
use Vaened\Authorization\Contracts\PermissionContract;
use Vaened\Authorization\Contracts\RoleContract;
use Vaened\Authorization\Resolvers\DriverResolver;
use Vaened\Authorization\Support\Config;
use Vaened\Authorization\Support\Determiner;
use Vaened\Authorization\Support\Helper;
use Vaened\Authorization\Support\Authenticated;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publish();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);

        $this->commands([
            InstallCommand::class
        ]);

        $this->registerBindings();
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [
            Authorizer::class,
        ];
    }

    private function publish(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/authorization.php' => base_path('config/authorization.php')
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_laravel_authorization_tables.stub' => database_path(
                    sprintf('migrations/%s_create_laravel_authorization_tables.php', date('Y_m_d_His'))
                ),
            ], 'migrations');
        }
    }

    private function registerBindings(): void
    {
        $this->configDriver();
        $this->app->bind(PermissionContract::class, Config::permissionModel());
        $this->app->bind(RoleContract::class, Config::roleModel());
        $this->app->singleton(Helper::class, Helper::class);
        $this->app->singleton(Authenticated::class, Authenticated::class);
        $this->configMiddleware();
    }

    private function configDriver(): void
    {
        (new DriverResolver($this->app))->make();
    }

    private function configMiddleware(): void
    {
        if (Determiner::isEnabledMiddleware()) {
            new Middleware($this->app->make('router'));
        }
    }
}
