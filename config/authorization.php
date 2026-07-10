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
    | Authorization Driver
    |--------------------------------------------------------------------------
    |
    | Here you may choose how the Laravel adapter resolves authorization
    | reads. The "database" driver always reads directly from persistence,
    | while the "cache" driver keeps authorization facts synchronized
    | through the cache-aware repository implementations.
    |
    */

    'driver' => 'database',

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

    /*
    |--------------------------------------------------------------------------
    | Authorization Cache
    |--------------------------------------------------------------------------
    |
    | Here you may configure the cache store used by the package when the
    | authorization driver is set to "cache". You may point it to a dedicated
    | store so the package remains isolated from the application's default
    | cache configuration.
    |
    */

    'cache' => [
        'store' => null,

        'prefix' => 'authorization',

        'ttl' => 3600,
    ],
];
