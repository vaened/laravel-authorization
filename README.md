Laravel Authorization
=====================

[![Build Status](https://travis-ci.org/eneav/laravel-authorization.svg)](https://travis-ci.org/eneav/laravel-authorization) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eneav/laravel-authorization/badges/quality-score.png)](https://scrutinizer-ci.com/g/eneav/laravel-authorization/)  [![Code Coverage](https://scrutinizer-ci.com/g/eneav/laravel-authorization/badges/coverage.png)](https://scrutinizer-ci.com/g/eneav/laravel-authorization/) [![StyleCI](https://styleci.io/repos/121161451/shield)](https://styleci.io/repos/121161451)  [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) 

Laravel Authorization is a package that provides a simple administration interface for roles and permissions.

```php
// create authorizations
$admin = $this->roles->create('Administrator');
$create = $this->permissions->create('Create Articles');
$edit = $this->permissions->create('Edit Articles');


// grant authorizations
$admin->grantMultiple([$edit, $create]);
$user->grant($editor);

// check
$user->isMemberOf('administrator'); // true
$user->can('create-articles'); // true
```

## Table of Contents
* [Installation](#installation)
* [Quick Start](#quick-start)
    - [checks](#checks)
    - [assignment](#assignment)
    - [revocation](#revocation)
* [Middleware](#middleware)
* [Blade Directives](#blade-directives)

## Installation
Laravel Authorization requires PHP 7.1 or 7.2. This version supports Laravel 5.5 or 5.6 only.

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
cant              | permission-name | boolean
cannot            | permission-name | boolean
isMemberOf        | role-name       | boolean
isntMemberOf      | role-name       | boolean

#### Example
```php
// verify if a user has a permission
$user->cant('permission-name');
// verify if a user does not have a permission
$user->cannot('permission-name');
// Verify if a user is a member of a role
$user->isMemberOf('role-name');
// verify if a user is not a member of a role
$user->isntMemberOf('role-name');
```
On the other hand, a role can only have permissions:
```php
// verify if a role has a permission
$role->cant('permission-name');
// verify if a role does not have a permission
$role->cannot('permission-name');
```

### Assignment
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
### Revocation
To revoke a permission or role of a model, you must use the `revoke` method:
```php
// revoke an authorization of a user
$user->revoke($authorization);
// revoke multiple authorizations of a user
$user->revokeMultiple([$permission, $role]);
// revoke a permission of a role
$role->revoke($permission);
// revoke multiple permissions of a role
$user->revokeMultiple([$firstPermission, $secondPermission]);
```
## Middleware
To use the available middleware, you must configure them in your [kernel](https://github.com/eneav/laravel-authorization-example/blob/master/app/Http/Kernel.php#L64-L65) file:
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
    // can not edit articles
@endauthenticatedCan
```
and to deny
```php
@authenticatedCannot('edit-articles')
    // ican not edit articles
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
