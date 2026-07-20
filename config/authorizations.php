<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Define the permissions that belong to your application. The array key is
    | the stable permission code used by Sentinel and Laravel Gate.
    |
    */

    'permissions' => [
        // 'users.read' => [
        //     'name' => 'Read users',
        //     'description' => 'Allows viewing users.',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Define the roles that belong to your application and the permission codes
    | assigned to each role. Set this to false when roles are managed outside
    | this file, such as through an administration panel. Run authorization:sync
    | after changing this file.
    |
    */

    'roles' => [
        // Set to false to disable role synchronization.
        // 'administrator' => [
        //     'name' => 'Administrator',
        //     'description' => 'Full access to the application.',
        //     'permissions' => [
        //         'users.read',
        //     ],
        // ],
    ],
];
