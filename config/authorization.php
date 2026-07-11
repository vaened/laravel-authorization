<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Gate Integration
    |--------------------------------------------------------------------------
    |
    | Configure how PHP Sentinel participates in Laravel's authorization Gate.
    | Set this to "before" when Sentinel should decide before Laravel's own
    | Gates and Policies. Set it to "after" when Sentinel should only decide
    | after Laravel cannot determine the result. When null, no Gate integration
    | is registered. The default is "after"; use null to disable integration.
    |
    | Sentinel always returns an allow or denial. With "before", a Sentinel
    | denial prevents Laravel from evaluating its own authorization rules. With
    | "after", an existing Laravel decision takes precedence and Sentinel acts
    | as a fallback.
    |
    */

    'gate' => 'after',

    /*
    |--------------------------------------------------------------------------
    | Authorization Cache
    |--------------------------------------------------------------------------
    |
    | Here you may configure the Laravel cache store used by the package.
    | Provide the name of one of the stores configured in your application's
    | "cache.stores" configuration to use it exclusively for authorization.
    | When null, the package uses your application's default cache store.
    |
    | A null TTL keeps projections permanently when the selected store supports
    | cache tags. Stores without tags use a twelve-hour TTL by default, so
    | projections orphaned after a global invalidation eventually expire.
    |
    */

    'cache' => [
        'store' => null,

        'prefix' => 'authorization',

        'ttl' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Tables
    |--------------------------------------------------------------------------
    |
    | Here you may configure the table names used by the Laravel adapter.
    | You may change them if your application already uses different names
    | or if you want Sentinel to integrate with an existing schema.
    |
    */

    'tables' => [
        'roles' => 'roles',

        'permissions' => 'permissions',

        'role_permissions' => 'role_permissions',

        'subject_roles' => 'subject_roles',

        'subject_permissions' => 'subject_permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may configure the middleware aliases registered by the package.
    | You may change them if your application already uses similar names or if
    | you want to expose a different public API for your route middleware.
    |
    */

    'middlewares' => [
        'permissions' => 'authorization.permissions',

        'roles' => 'authorization.roles',
    ],
];
