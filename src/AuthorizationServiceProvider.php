<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Enea\Authorization\Support\Config;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBindings();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bind contracts with concrete objects.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $this->configDriver();
        $this->app->bind(PermissionContract::class, Config::permissionModel());
        $this->app->bind(RoleContract::class, Config::roleModel());
    }

    private function configDriver(): void
    {
        (new DriversResolver($this->app))->make();
    }
}
