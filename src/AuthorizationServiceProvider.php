<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization;

use Enea\Authorization\Commands\InstallCommand;
use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Resolvers\DriverResolver;
use Enea\Authorization\Support\Config;
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
        $this->publishes([
            __DIR__ . '/../config/authorization.php' => base_path('config/authorization.php')
        ]);
    }

    private function registerBindings(): void
    {
        $this->configDriver();
        $this->app->bind(PermissionContract::class, Config::permissionModel());
        $this->app->bind(RoleContract::class, Config::roleModel());
    }

    private function configDriver(): void
    {
        (new DriverResolver($this->app))->make();
    }
}
