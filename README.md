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

Publish the package resources:

```bash
php artisan vendor:publish --tag=laravel-authorization-config
php artisan vendor:publish --tag=laravel-authorization-migrations
```

Then run your migrations:

```bash
php artisan migrate
```

## Configuration

Laravel Authorization only needs one application-level integration step to work: your user model must become authorizable.

### Making your user model authorizable

Laravel Authorization does not require you to extend a package-specific user model.

Instead, the user model you want to make authorizable only needs to:

- implement [`Authorizable`](src/Authorizable.php)
- use [`UsesAuthorizations`](src/UsesAuthorizations.php)

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\UsesAuthorizations;

class User extends Authenticatable implements Authorizable
{
    use UsesAuthorizations;
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

Laravel Authorization registers two route middleware aliases that protect routes through permission or role checks.

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

### `authorization:cache:invalidate`

Invalidates every authorization projection managed by the package:

```bash
php artisan authorization:cache:invalidate
```

Use it after authorization data is changed outside Laravel Authorization, such
as through a direct database operation or an external integration.

## Default models and repositories

This package provides the Laravel-side infrastructure for [PHP Sentinel](https://github.com/vaened/php-sentinel):

- Eloquent repositories
- package configuration
- middleware integration
- service provider wiring

It also includes default models for roles and permissions, plus a convenience `Subject` model.  
However, the intended integration point for your application user model is the `Authorizable` contract and `UsesAuthorizations` trait.

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