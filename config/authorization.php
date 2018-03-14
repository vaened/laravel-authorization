<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Default Driver
      |--------------------------------------------------------------------------
      |
      | This option controls the default "driver" that will be used to
      | access the authorization repository, by default the "database" driver is used.
      |
      | Supported: "database"
      */
    'driver' => 'database',

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
        'role' => 'roles',

        /// Table containing the permissions.
        'permission' => 'permissions',

        /// Table containing the roles that belong to a role.
        'role_has_many_permissions' => 'role_permissions',

        /// Table that stores all roles per authorized user.
        'user_roles' => 'user_roles',

        /// Table that stores all permissions per authorized user.
        'user_permissions' => 'user_permissions',
    ],
    'listeners' => [
        // write in the log every time access to a protected route is denied with the authorization middleware.
        'unauthorized-owner-logger' => true,
    ],
];
