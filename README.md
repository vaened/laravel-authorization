Laravel Authorization
=====================

[![Build Status](https://github.com/vaened/laravel-authorization/actions/workflows/tests.yml/badge.svg)](https://github.com/vaened/laravel-authorization/actions?query=workflow%3ATests) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vaened/laravel-authorization/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vaened/laravel-authorization/?branch=master)  [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) 

Laravel Authorization is a package that provides a simple administration interface for roles and permissions.

```php
// create authorizations
$cashier = $this->roles->create('Cashier');
$create = $this->permissions->create('Create Documents');
$annul = $this->permissions->create('Annul Documents');


// grant authorizations
$cashier->grantMultiple([$create, $annul]);
$user->grant($cashier);

// check
$user->isMemberOf('cashier'); // true
$user->can('create-documents'); // true
$user->can('annul-documents'); // true

// deny authorizations
$user->deny('annul-documents');

// now
$user->can('annul-documents'); // false

```

## Table of Contents
* [Installation](#installation)
* [Quick Start](#quick-start)
    - [checks](#checks)
    - [`GRANT`](#grant)
    - [`REVOKE`](#revoke)
    - [`DENY`](#deny)
* [Middleware](#middleware)
* [Blade Directives](#blade-directives)

## Installation
Laravel Authorization requires PHP 7.4. This version supports Laravel 8 only.

*if you need to use it in laravel 7, you can use version enea/laravel-authorization@1.2*

To get the latest version, simply require the project using Composer:
```sh
$ composer require enea/laravel-authorization
```

Once installed, if you are not using automatic package discovery, then you need to register the [Enea\Authorization\AuthorizationServiceProvider](https://github.com/eneav/laravel-authorization/blob/master/src/AuthorizationServiceProvider.php) service provider in your `config/app.php`.

and finally, it only remains to run in the console:
```sh
$ php artisan authorization:install
```

## Quick Start
Starting with laravel-authorization is as simple as extending the `User` model that provides the package:
``` php
use Enea\Authorization\Models\User as Authorizable;

class User extends Authorizable {
    //
}
```
Or in case you need to customize your user model, you must implement the `Enea\Authorization\Contracts\Authorisable` interface and use the `Enea\Authorization\Traits\Authorisable` trait:
``` php
use Enea\Authorization\Contracts\Authorizable as AuthorizableContract;
use Enea\Authorization\Traits\Authorizable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
}
```
### Checks
There are some methods available for checking roles and permissions:

Method            | Parameter       | Return
------------------|-----------------|------------------
can               | permission-name | boolean
cannot            | permission-name | boolean
isMemberOf        | role-name       | boolean
isntMemberOf      | role-name       | boolean

#### Example
```php
// verify if a user has a permission
$user->can('permission-name');
// verify if a user does not have a permission
$user->cannot('permission-name');
// verify if a user is a member of a role
$user->isMemberOf('role-name');
// verify if a user is not a member of a role
$user->isntMemberOf('role-name');
```
On the other hand, a role can only have permissions:
```php
// verify if a role has a permission
$role->can('permission-name');
// verify if a role does not have a permission
$role->cannot('permission-name');
```

### GRANT
Simplify the way in which roles and permissions are granted, both can be granted through the `grant` method in your model, you can see an example [here](https://github.com/eneav/laravel-authorization-example/blob/master/database/seeds/AuthorizationsSeeder.php)

```php
// grant an authorization to user
$user->grant($authorization);
// grant multiple authorizations to user
$user->grantMultiple([$permission, $role]);
// grant a permission to role
$role->grant($permission);
// grant multiple permissions to role
$user->grantMultiple([$firstPermission, $secondPermission]);
```
### REVOKE
To revoke a permission or role of a model, you must use the `revoke` or `revokeMultiple` method:
```php
// revoke an authorization to a user
$user->revoke($authorization);
// revoke multiple authorizations of a user
$user->revokeMultiple([$permission, $role]);
// revoke a permission to a role
$role->revoke($permission);
// revoke multiple permissions of a role
$user->revokeMultiple([$firstPermission, $secondPermission]);
```

### DENY
To prohibit certain accesses to a user can do it through the method `deny` and `denyMultiple`:
```php
// deny a permission to a user
$user->deny($permission);
// deny multiple permissions to a user
$user->denyMultiple($permissions);
```
## Middleware
The middleware are activated automatically from the beginning, to change this you can do it from the [configuration](https://github.com/eneav/laravel-authorization/blob/master/config/authorization.php) file:
```php
    // automatic middleware configuration.
    'middleware' => [
        'enabled' => true,

        'permissions' => [
            'alias' => 'authenticated.can',
            'class' => \Enea\Authorization\Middleware\PermissionAuthorizerMiddleware::class,
        ],
        'roles' => [
            'alias' => 'authenticated.is',
            'class' => \Enea\Authorization\Middleware\RoleAuthorizerMiddleware::class,
        ],
    ],

```
Or in case you want to do a manual configuration you can deactivate the automatic load and modify your [kernel](https://github.com/eneav/laravel-authorization-example/blob/master/app/Http/Kernel.php#L64-L65) file:

```php
protected $routeMiddleware = [
    ...
    
    // laravel-authorization
    'authenticated.can' => \Enea\Authorization\Middleware\PermissionAuthorizerMiddleware::class,
    'authenticated.is' => \Enea\Authorization\Middleware\RoleAuthorizerMiddleware::class,
];
```
Then you can use it in your routes like any other [middleware](https://github.com/eneav/laravel-authorization-example/blob/master/routes/web.php#L33-L40):

```php
$router->get('create', 'CreateController@create')->middleware('authenticated.can:create-articles');
$router->get('admin', 'DashboardController@index')->middleware('authenticated.is:admin');
```

In case any user tries to access a protected route without authorization, an exception of type [`UnauthorizedOwnerException`](https://github.com/eneav/laravel-authorization/blob/master/src/Exceptions/UnauthorizedOwnerException.php) will be throw.

### Custom errors
To show a custom error, we can edit the [`Handler`](https://github.com/eneav/laravel-authorization-example/blob/master/app/Exceptions/Handler.php#L52-L54) file:
```php
public function render($request, Exception $exception)
{
    if ($exception instanceof UnauthorizedOwnerException) {
        return redirect()->route('custom-unauthorized-route');
    }
    return parent::render($request, $exception);
}
```
## Blade Directives
This package also adds Blade directives to verify if the currently connected user has a specific role or permission.
Optionally you can pass in the `guard` that the check will be performed on as a second argument.
### For Roles
```php
@authenticatedIs('articles-owner')
    // is articles owner
@else
    // it's not articles owner
@endauthenticatedIs
```
and to deny
```php
@authenticatedIsnt('articles-owner')
    // it's not articles owner
@else
    // is articles owner
@endauthenticatedIsnt
```
### For Permissions
```php
@authenticatedCan('edit-articles')
    // can edit articles
@else
    // cannot edit articles
@endauthenticatedCan
```
and to deny
```php
@authenticatedCannot('edit-articles')
    // cannot edit articles
@else
    // can edit articles
@endauthenticatedCannot
```

## Examples
[Simple CRUD](https://github.com/eneav/laravel-authorization-example)

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License
Laravel Authorization is licensed under [The MIT License (MIT)](LICENSE.md).
