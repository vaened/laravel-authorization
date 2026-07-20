# Laravel Authorization

[![Tests](https://github.com/vaened/laravel-authorization/actions/workflows/tests.yml/badge.svg)](https://github.com/vaened/laravel-authorization/actions/workflows/tests.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Roles, permissions, explicit denials, and route middleware for Laravel applications.

Built on top of [PHP Sentinel](https://github.com/vaened/php-sentinel).

```php
// Authorizations
$cashier         = $this->roles->create('cashier', 'Cashier');
$createDocuments = $this->permissions->create('documents.create', 'Create Documents');
$annulDocuments  = $this->permissions->create('documents.annul', 'Annul Documents');

// Assignment
$cashier->grant($createDocuments, $annulDocuments);
$user->grant($cashier);

// Evaluation
$user->actsAs('cashier');             // true
$user->can('documents.create');       // true
$user->can('documents.annul');        // true

// Deny overrides direct or inherited grants
$user->deny($annulDocuments);
$user->can('documents.annul');        // false
```

## Installation

Laravel Authorization requires PHP 8.4 or higher and can be installed via Composer:

```bash
composer require vaened/laravel-authorization
```

Publish the package resources with the installer:

```bash
php artisan authorization:install
```

The installer can publish three resources:

- **Package configuration** — runtime settings for tables, cache, middleware, and Laravel Gate integration.
- **Authorization definitions** — the application's roles and permissions used by `authorization:sync`.
- **Database migrations** — the tables required to store roles, permissions, and their assignments.

Existing configuration files and migrations are skipped and never overwritten.

You can also publish each resource independently with its `vendor:publish` tag:

```bash
php artisan vendor:publish --tag=laravel-authorization-config
php artisan vendor:publish --tag=laravel-authorization-definitions
php artisan vendor:publish --tag=laravel-authorization-migrations
```

Then run your migrations:

```bash
php artisan migrate
```

## Configuration

By default, your user model uses the package's direct authorization API through the `Authorizable` interface and `Authorizations` trait, and
Sentinel integrates
with Laravel's Gate using the `after` strategy. See [Advanced usage](#advanced-usage) when you prefer Laravel's native API.

### Using the direct model API

Laravel Authorization does not require you to extend a package-specific user model.

Instead, the user model you want to make authorizable only needs to:

- implement [`Authorizable`](src/Authorizable.php)
- use [`Authorizations`](src/Authorizations.php)

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorizations;

class User extends Authenticatable implements Authorizable
{
    use Authorizations;
}
```

Once your user model uses the contract and trait above, it gains these capabilities:

| Method                                           | Description                                                        |
|--------------------------------------------------|--------------------------------------------------------------------|
| `can(string ...$permissions): bool`              | Checks whether the user has at least one of the given permissions. |
| `cannot(string ...$permissions): bool`           | Inverse of `can`.                                                  |
| `actsAs(string ...$roles): bool`                 | Checks whether the user has at least one of the given roles.       |
| `actsNotAs(string ...$roles): bool`              | Inverse of `actsAs`.                                               |
| `grant(Authorization ...$authorizations): void`  | Grants roles or permissions to the user.                           |
| `deny(Permission ...$permissions): void`         | Explicitly denies permissions to the user.                         |
| `revoke(Authorization ...$authorizations): void` | Removes a previous grant or denial from the user.                  |

## Authorization management

Use PHP Sentinel's `RoleRegistry` and `PermissionRegistry` to manage the role
and permission catalogs. Both registries expose the same API; their only
difference is the authorization type they manage.

```php
use Vaened\Sentinel\Registry\PermissionRegistry;
use Vaened\Sentinel\Registry\RoleRegistry;

final readonly class AuthorizationCatalog
{
    public function __construct(
        private RoleRegistry $roles,
        private PermissionRegistry $permissions,
    ) {
    }
}
```

| Method                                                               | Description                                                    | `RoleRegistry` result | `PermissionRegistry` result |
|----------------------------------------------------------------------|----------------------------------------------------------------|-----------------------|-----------------------------|
| `create(string $code, string $name, ?string $description = null)`    | Creates a catalog entry.                                       | `Role`                | `Permission`                |
| `lookup(array $codes)`                                               | Retrieves the entries whose codes were requested.              | `Roles`               | `Permissions`               |
| `find(string $code)`                                                 | Retrieves one entry by code, or `null` when it does not exist. | `Role\|null`          | `Permission\|null`          |
| `update(int\|string $id, string $name, ?string $description = null)` | Updates an existing entry.                                     | `void`                | `void`                      |
| `remove(int\|string $id)`                                            | Removes an existing entry when it is no longer assigned.       | `void`                | `void`                      |

```php
$cashier = $this->roles->create('cashier', 'Cashier');
$read = $this->permissions->create('documents.read', 'Read Documents');

$cashier->grant($read);

$permissions = $this->permissions->lookup(['documents.read', 'documents.update']);
$permission = $this->permissions->find('documents.read');
```

## Middleware

When Gate integration is enabled (the default), you can use Laravel's native
`can` middleware for permission checks:

```php
Route::middleware('can:posts.edit')->group(function () {
    // ...
});
```

Laravel's `can` middleware uses the Gate integration described in
[Laravel Gate](#laravel-gate). It is available as long as
`authorization.gate` is not `null`.

Laravel Authorization also registers two package middleware aliases. They are useful when you want to invoke Sentinel directly,
including when Gate integration is disabled, and when you need to check roles.

- `authorization.permissions` allows the request only if the current authenticated user can perform at least one of the given permissions.
- `authorization.roles` allows the request only if the current authenticated user acts as at least one of the given roles.

```php
Route::middleware('authorization.permissions:posts.edit')->group(function () {
    // ...
});

Route::middleware('authorization.roles:admin')->group(function () {
    // ...
});
```

If authorization fails, the middleware throws Laravel’s `AuthorizationException`.

You can rename these aliases by publishing and editing the `middlewares` array in
[`config/authorization.php`](config/authorization.php).

## Laravel Gate

Laravel Authorization can connect PHP Sentinel to Laravel's authorization Gate.
This lets a compatible subject participate in Laravel's standard authorization
features, including `Gate::allows`, the `can` route middleware, and Blade's
`@can` directive.

Configure the `gate` option in
[`config/authorization.php`](config/authorization.php):

```php
'gate' => 'after', // 'after', 'before', or null
```

The default is `after`. Choose another strategy only when your application
needs different precedence:

| Strategy | Behavior                                                                                                     | Use it when                                                             |
|----------|--------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------|
| `before` | Sentinel evaluates the ability before Laravel's own Gates and Policies. Its result always decides the check. | Sentinel is the authoritative authorization system for the application. |
| `after`  | Laravel evaluates its own Gates and Policies first. Sentinel evaluates only when Laravel has no result.      | Recommended default; Sentinel acts as a fallback.                       |
| `null`   | No Sentinel callback is registered in Laravel's Gate.                                                        | The application should use Sentinel directly or manage Gate itself.     |

Sentinel always resolves an ability to `true` or `false`: a subject either has the permission or it does not. It does not return
Laravel's undecided `null` result. Consequently, `before` also denies abilities that Sentinel does not grant, while `after` preserves
any explicit allow or denial already returned by Laravel.

## Cache

Laravel Authorization caches each subject's authorization projection: its roles
and the effective state of its permissions. The cache is updated or invalidated
by the package when authorization assignments change.

You can configure it through the `cache` array in
[`config/authorization.php`](config/authorization.php).

- `store` is the name of a store defined in your application's
  [`cache.stores`](https://laravel.com/docs/cache#configuration) configuration.
  Set it when authorization should use a dedicated Laravel cache store. When it
  is `null`, the package uses your application's default cache store.
- `prefix` namespaces the package's authorization cache entries so they remain
  isolated from other cached application data.
- `ttl` is the lifetime, in seconds, of a subject authorization projection. When
  it is `null` and the selected store supports cache tags, projections are kept
  permanently because the package can remove them explicitly. Stores without
  tag support use a twelve-hour TTL by default, so projections orphaned after a
  global invalidation eventually expire. Set an integer TTL to override it.

## Database

The package ships with five tables that back the entire authorization model:

| Table                 | What it stores                                                                                                            |
|-----------------------|---------------------------------------------------------------------------------------------------------------------------|
| `permissions`         | Atomic permissions (e.g. `users.read`, `posts.publish`). The catalog.                                                     |
| `roles`               | Named groupings of permissions. The catalog.                                                                              |
| `role_permissions`    | Which permissions each role grants. Many-to-many between `roles` and `permissions`.                                       |
| `subject_roles`       | Which roles each subject carries. Polymorphic — works with any authorizable model.                                        |
| `subject_permissions` | Direct grants and explicit denials on a subject. Polymorphic. A denial takes precedence over a direct or inherited grant. |

You can rename any of these tables by publishing and editing the `tables` array in
[`config/authorization.php`](config/authorization.php). Each key corresponds to a table above.

## Commands

### `authorization:install`

Publishes the package configuration, authorization definitions, and database migrations.
It lets you select the resources interactively and skips any resource that already
exists. Use the arrow keys to navigate, space to select, and Enter to confirm.

```bash
php artisan authorization:install
```

### `authorization:sync`

Synchronizes the application's configured roles and permissions. See
[Authorization synchronization](#authorization-synchronization) for details.

```bash
php artisan authorization:sync
```

### `authorization:cache:invalidate`

Invalidates every authorization projection managed by the package:

```bash
php artisan authorization:cache:invalidate
```

Use it after authorization data is changed outside Laravel Authorization, such as through a direct database operation or an external
integration.

## Authorization synchronization

Authorization synchronization lets you define the application's roles and permissions in a configuration file and reconcile that
definition with the authorization database through the [`authorization:sync`](#authorizationsync) command.

This feature is optional. Use it when you want to define application roles and permissions in code and synchronize them with the database.
If your application manages authorization records through seeders, registries, or an administrative interface, you can omit this file and
the synchronization command.

The definitions file is the source of truth for the permissions and roles that belong to the application. By default, it is:

```text
config/authorizations.php
```

You can change the filename through `authorization.synchronization.config` in [`config/authorization.php`](config/authorization.php).
Use the configuration key without the `.php` extension.

The file defines permissions by code and roles with their assigned permission codes:

```php
return [
    'permissions' => [
        'users.read' => [
            'name' => 'Read users',
        ],
    ],
    'roles' => [
        'editor' => [
            'name'        => 'Editor',
            'permissions' => ['users.read'],
        ],
    ],
];
```

Run the [`authorization:sync`](#authorizationsync) command after changing the file. It creates missing entries, updates their metadata, and
reconciles the permissions assigned to each role.

Use the optional `--prune` flag to remove roles and permissions that are no longer present in the file:

```bash
php artisan authorization:sync --prune
```

Pruning is disabled by default and does not remove entries that are still in
use.

## Default models and repositories

This package provides the Laravel-side infrastructure for [PHP Sentinel](https://github.com/vaened/php-sentinel):

- Eloquent repositories
- package configuration
- middleware integration
- service provider wiring

It also includes default models for roles and permissions. When using the direct model API, your application user is the authorization
subject: implement the `Authorizable` contract and use the `Authorizations` trait.

## Advanced usage

The default setup is documented in [Using the direct model API](#using-the-direct-model-api) and [Laravel Gate](#laravel-gate). This
section only covers the alternative integration where the model uses Laravel's native authorization API.

### Using Laravel's native authorization API

Use this mode when the application should use Laravel's own authorization API and does not need the package's direct model methods. Do
not use the package's `Authorizable` interface or `Authorizations` trait. The model must implement Sentinel's `Subject` contract:

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vaened\Sentinel\Identifier;
use Vaened\Sentinel\Subject;

class User extends Authenticatable implements Subject
{
    public function id(): int|string|Identifier
    {
        return $this->getKey();
    }
}
```

`Illuminate\\Foundation\\Auth\\User` already includes Laravel's native
`Authorizable` trait. If your model extends Eloquent's base `Model` directly,
use [
`Illuminate\\Foundation\\Auth\\Access\\Authorizable`](https://github.com/laravel/framework/blob/13.x/src/Illuminate/Foundation/Auth/Access/Authorizable.php)
on the model instead.

Use Laravel's own authorization implementation on the model. Keep
`gate => 'after'` to let Sentinel serve as a fallback, use `before` only when
Sentinel must take precedence, or use `null` when Laravel must operate without
Sentinel Gate integration. See [Laravel Gate](#laravel-gate) for the exact
precedence rules.

Without the package trait, manage assignments through the package facades:

```php
use Vaened\Authorization\Facades\Denier;
use Vaened\Authorization\Facades\Granter;
use Vaened\Authorization\Facades\Revoker;

Granter::grant($user, $role);
Denier::deny($user, $permission);
Revoker::revoke($user, $permission);
```

> **Custom trait:** If you want to expose these operations as methods on your
> model, create a custom trait based on
> [`Authorizations`](src/Authorizations.php) and keep only the methods you
> need, such as `grant`, `deny`, and `revoke`. Omit `can` because Laravel
> provides that authorization API in this mode.

Do not combine the package's `Authorizations` trait with Laravel's `Authorizable` trait. Both define `can` and `cannot` with different
contracts.

## Errors

Adapter-specific errors extend Sentinel’s base [
`AuthorizationError`](https://github.com/vaened/php-sentinel/blob/master/src/Errors/AuthorizationError.php).

For example, if a subject used by the Laravel adapter does not extend Eloquent `Model`, the package throws:

- `UnsupportedSubject`

Middleware authorization failures continue to use Laravel’s own `AuthorizationException`.

## Development

```bash
make composer-install
make test
```

## Additional documentation

You can find more details in the source code as well as in the tests located in [`tests/`](tests).

The tests cover different usage scenarios and can serve as additional reference for understanding the library’s behavior.
