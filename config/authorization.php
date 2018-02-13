<?php

return [
    'models' => [
        /*
          |--------------------------------------------------------------------------
          | Role Model
          |--------------------------------------------------------------------------
          |
          | This will be the model that will be used to represent the roles in the database,
          | if you want to have your own model to follow, you can extend this base model
          | or implement the contracts 'AuthorizedContract', 'RoleContract',
          | also use the traits 'Grantable' and 'HasPermission'
          |
          */
        'role' => \Enea\Authorization\Models\Role::class,

        /*
          |--------------------------------------------------------------------------
          | Permission Model
          |--------------------------------------------------------------------------
          |
          | This will be the model that will be used to represent the permissions in the database,
          | if you want to have your own permissions model, you can extend this base model
          | or implement the contracts 'AuthorizedContract', ' PermissionContract',
          | also have to use the traits 'Grantable' and 'HasPermission'
          |
          */
        'permission' => \Enea\Authorization\Models\Permission::class,
    ],

    'tables' => [
        /// Table containing the roles.
        'role' => 'role',

        /// Table containing the permissions.
        'permission' => 'permissions',

        /// Table containing the roles that belong to a role.
        'role_has_many_permissions' => 'role_permissions',

        /// Table that stores all permissions and roles per authorized user.
        'authorizations' => 'authorizations',
    ],

    'cache' => [
        /// Prefix for key name of all permissions in cache.
        'prefix_key' => 'enea-laravel-authorization',

        /// Time in minutes to allow cached permissions.
        'expiration_time' => 60 * 24
    ]
];
