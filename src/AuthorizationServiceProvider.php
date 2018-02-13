<?php
/**
 * Created on 12/02/18 by enea dhack.
 */

namespace Enea\Authorization;

use Enea\Authorization\Contracts\PermissionContract;
use Enea\Authorization\Contracts\RoleContract;
use Illuminate\Database\Eloquent\Relations\Relation;
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
    }

    /**
     * Bind contracts with concrete objects.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $config = $this->app->make('config')->get('authorization.models');
        $this->app->bind(PermissionContract::class, $config['permission']);
        $this->app->bind(RoleContract::class, $config['role']);

        Relation::morphMap([
            'permission' => $config['permission'],
            'role' => $config['role'],
        ]);
    }
}
