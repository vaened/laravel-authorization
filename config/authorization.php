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
];
