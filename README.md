# Laravel Authorization

[![Tests](https://github.com/vaened/laravel-authorization/actions/workflows/tests.yml/badge.svg)](https://github.com/vaened/laravel-authorization/actions/workflows/tests.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

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

By default, your Laravel user model uses Laravel's native authorization checks
and the package's direct assignment API through the `Authorizable` interface
and `Authorize` trait. Sentinel integrates with Laravel's Gate using the
`after` strategy.

### Using the direct model API

Laravel Authorization does not require you to extend a package-specific user model.

Instead, the user model you want to make authorizable only needs to:

- implement [`Authorizable`](src/Authorizable.php)
- use [`Authorize`](src/Authorize.php) for grants, denials, and revocations

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorize;

class User extends Authenticatable implements Authorizable
{
    use Authorize;
}
```

The Laravel `Authenticatable` base model already provides Laravel's native
`can` and `cannot` methods. If the model extends Eloquent's base `Model`
directly and should expose Sentinel's checks instead, use
[`Abilities`](src/Abilities.php) in addition to `Authorize`.

```php
use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Abilities;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorize;

class User extends Model implements Authorizable
{
    use Authorize, Abilities;
}
```

The available methods depend on the model and traits in use. Laravel's native
authorization API and `Abilities` are alternative APIs; do not use both on the
same model.

| Method                                           | Laravel native                         | `Abilities`                | `Authorize`                   |
|--------------------------------------------------|----------------------------------------|----------------------------|-------------------------------|
| `can` / `cannot`                                 | Laravel abilities, arguments, policies | Sentinel permission checks | —                             |
| `actsAs` / `actsNotAs`                           | —                                      | Sentinel role checks       | —                             |
| `grant(Authorization ...$authorizations): void`  | —                                      | —                          | Grants roles or permissions   |
| `deny(Permission ...$permissions): void`         | —                                      | —                          | Explicitly denies permissions |
| `revoke(Authorization ...$authorizations): void` | —                                      | —                          | Removes a grant or denial     |

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

- `authorization.permissions` allows the request only if the resolved authorization subject can perform at least one of the given
  permissions.
- `authorization.roles` allows the request only if the resolved authorization subject acts as at least one of the given roles.

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

Most applications do not need to access the cache store directly. The package
uses [`AuthorizationCacheStore`](https://github.com/vaened/php-sentinel/blob/master/src/Cache/AuthorizationCacheStore.php)
to manage subject authorization projections. It can read, store, forget a
single subject's projection, or invalidate every projection. If authorization
data for one subject is changed outside the package, you can forget only that
subject's projection:

```php
use Vaened\Sentinel\Cache\AuthorizationCacheStore;

app(AuthorizationCacheStore::class)->forget($user);
```

For a global invalidation, call the store directly:

```php
app(AuthorizationCacheStore::class)->invalidate();
```

The [`authorization:cache:invalidate`](#authorizationcacheinvalidate) command
is the console convenience wrapper around that same operation. Assignments
should still be changed through the package operators or registries, not by
writing projections directly.

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

### Subject identifiers

The polymorphic `authorizable` columns use Laravel's `morphs()` method and
respect Laravel's configured morph key type.

If your subjects use UUIDs or ULIDs, configure Laravel before running the
migrations, or replace `morphs()` in the published migration with
`uuidMorphs()` or `ulidMorphs()`. If your application mixes identifier types,
define the `authorizable_type` and `authorizable_id` columns manually using a
compatible string type.

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

Each section can be disabled independently with `false` (or `null`):

```php
return [
    'permissions' => [
        // Application permissions...
    ],
    'roles' => false,
];
```

In this example, permissions are synchronized while roles remain managed externally, such as through an administration panel. A section
set to `false`, `null`, or omitted is not synchronized and is never pruned. An empty array is different: it enables synchronization and
declares that no entries are expected for that section.

Run the [`authorization:sync`](#authorizationsync) command after changing the file. It creates missing entries, updates their metadata, and
reconciles the permissions assigned to each role.

Use the optional `--prune` flag to remove roles and permissions that are no longer present in the file:

```bash
php artisan authorization:sync --prune
```

> Pruning is disabled by default and does not remove entries that are still in use.

## Default models and repositories

This package provides the Laravel-side infrastructure for [PHP Sentinel](https://github.com/vaened/php-sentinel):

- Eloquent repositories
- package configuration
- middleware integration
- service provider wiring

It also includes default models for roles and permissions. Your application user, or a membership representing that user in an organization,
is the authorization subject: implement the `Authorizable` contract and use the `Authorize` trait. Use `Abilities` only when you need the
package's additional role and permission checks on a model that does not already expose Laravel's authorization methods.

## Multitenancy

Every authorization `Subject` has exactly one direct scope or no scope. A
subject cannot represent multiple organizations at the same time. The correct
model depends on whether a user belongs to one organization or to many.

### One organization per user

When a user belongs to one organization only, the user can remain the
authorization subject:

```php
use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorize;
use Vaened\Sentinel\Subject;

final class User extends Model implements Authorizable
{
    use Authorize;

    public function scope(): Subject|null
    {
        return $this->organization;
    }
}
```

The `Authorize` trait already provides:

- `id()`, using the model's primary key.
- `scope()`, returning `null` by default.
- `grant()`.
- `deny()`.
- `revoke()`.

Therefore, a non-multitenant application does not need to override `scope()`.
An organization can implement `Authorizable` and use `Authorize` without
overriding `scope()`, because the default `null` scope is correct for the
root of the hierarchy:

```php
use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorize;

final class Organization extends Model implements Authorizable
{
    use Authorize;
}
```

### Multiple organizations per user

When a user can belong to multiple organizations, the user cannot be the
authorization subject: one user cannot return multiple scopes from
`scope()`. The membership between the user and an organization becomes the
subject instead.

```text
User 10
├── Membership 100 → Organization A
└── Membership 200 → Organization B
```

Each membership has exactly one organization and therefore exactly one scope:

```php
use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorize;
use Vaened\Sentinel\Subject;

final class Membership extends Model implements Authorizable
{
    use Authorize;

    public function scope(): Subject|null
    {
        return $this->organization;
    }
}
```

The application must resolve the membership for the current request. Configure
an `AuthorizationSubjectResolver` when the authenticated user is not itself
the subject. The tenant can come from any source defined by the application,
such as a route parameter, route model binding, a request header, a subdomain,
or a dedicated tenancy service.

This example accepts either a route value or an `X-Organization-Id` header. Use
the source that matches your application's tenancy model:

The resolver returns a `SubjectResolution` with one of these states:

- `found`: the subject was found and can be evaluated.
- `notFound`: the context is available, but no subject exists.
- `unavailable`: there is not enough context to resolve the subject yet; this
  result is not cached.

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Authorization\Resolvers\SubjectResolution;
use Vaened\Sentinel\Subject;

final class MembershipSubjectResolver implements AuthorizationSubjectResolver
{
    public function resolve(object|null $user, Request $request): SubjectResolution
    {
        if (!$user instanceof User) {
            return SubjectResolution::notFound();
        }

        $organization = $request->route('organization')
            ?? $request->header('X-Organization-Id');

        $organizationId = $organization instanceof Model
            ? $organization->getKey()
            : $organization;

        if ($organizationId === null) {
            return SubjectResolution::unavailable();
        }

        $membership = $user->memberships()
            ->where('organization_id', $organizationId)
            ->first();

        return $membership === null
            ? SubjectResolution::notFound()
            : SubjectResolution::found($membership);
    }
}
```

```php
'subject' => [
    'resolver' => MembershipSubjectResolver::class,
],
```

Gate, middleware, and package authorization services then evaluate the
resolved membership rather than the authenticated user.

### Roles and scopes

A role can be global or associated with a specific scope.

| Subject        | Role           | Result   |
|----------------|----------------|----------|
| No scope       | Global         | Allowed  |
| No scope       | Organization A | Rejected |
| Organization A | Global         | Allowed  |
| Organization A | Organization A | Allowed  |
| Organization A | Organization B | Rejected |

A global role can be assigned to any subject. A role associated with an organization can only be assigned to subjects in the same scope.

The scope also limits which permissions can be assigned:

- A direct permission must be compatible with the subject's scope.
- A permission assigned to a role must be compatible with the role's scope.
- A role must be compatible with the subject's scope before it can be assigned.
- An explicit denial does not require scope approval because it only reduces access.
- `revoke()` can remove old or invalid assignments even when they are no longer scope-compatible.

If a scope validation fails, the complete operation is rejected and no partial authorization is stored.

### Permission evaluation

When evaluating through the package's `Abilities` trait or `Authorizer` facade,
the resolved subject is evaluated against its direct permissions, inherited
permissions, and applicable scopes:

```php
$user->can('documents.read');
```

Sentinel evaluates:

1. The subject's direct permissions.
2. Permissions inherited through the subject's roles.
3. Permissions from the direct scope.
4. Permissions from higher-level scopes, according to the configured propagation policy.

Roles are reduced to the permissions they contain. The check does not authorize a role by itself; it determines whether one of the role's
permissions allows the requested action.

An explicit denial overrides any direct or inherited grant.

### `OR` and `AND`

The package's `Abilities` methods accept multiple permissions or roles and use
`OR` by default.

This is separate from Laravel's native `can()` and Gate API. Laravel's
`Gate::allows()` and `Gate::check()` require every ability in an array to be
allowed; use `Gate::any()` when any one of the abilities is enough. Do not
combine Laravel's native `can()` with the package's `Abilities` trait on the
same model.

| Operator | Result                                       |
|----------|----------------------------------------------|
| `OR`     | At least one requested code must be allowed. |
| `AND`    | Every requested code must be allowed.        |

For example:

```php
$user->can('documents.read', 'documents.update');
```

With `OR`, the check succeeds if the user can perform at least one of the two actions.

With `AND`, both actions must be allowed:

```php
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Sentinel\Authorization\Junction;

Authorizer::can(
    $user,
    ['documents.read', 'documents.update'],
    Junction::And,
);
```

Scope evaluation applies the same authorization rule at every participating level. Sentinel does not combine different permissions from
different levels to produce a valid result. For example, if the user has `documents.read` and the organization has `documents.update`, that
does not satisfy an `OR` check when neither same permission is allowed at every evaluated level.

The Sentinel `Authorizer` applies the same operators to role checks. The model
helpers `can()` and `actsAs()` use `OR` by default and do not expose a junction
argument. Use the `Authorizer` directly when an `AND` role check is required:

```php
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Sentinel\Authorization\Junction;

Authorizer::is(
    $user,
    ['editor', 'reviewer'],
    Junction::And,
);
```

### Scope propagation

Propagation determines which scopes participate in permission evaluation.

Configure it in `config/authorization.php`:

```php
'propagation' => \Vaened\Sentinel\Propagation\TransitiveScopePropagationPolicy::class,
```

#### Transitive propagation

This is the default policy. It evaluates the direct scope and all of its ancestors.

With this hierarchy:

```text
User → Team → Organization
```

Sentinel evaluates:

1. The user.
2. The team.
3. The organization.

Use it when permissions should be inherited through the entire hierarchy.

#### Direct propagation

This policy evaluates only the immediate scope:

```php
'propagation' => \Vaened\Sentinel\Propagation\DirectScopePropagationPolicy::class,
```

With the same hierarchy, Sentinel evaluates:

1. The user.
2. The team.

The organization does not participate directly in the user's evaluation.

Use it when permissions should only be inherited from the immediate tenant or scope.

Propagation affects permission checks through `can()` and `cannot()`. `actsAs()` and `actsNotAs()` check whether the subject has the role;
they do not automatically traverse the scope hierarchy to find roles.

### Scope errors

`scope()` must return a `Subject` or use the `null` default provided by `Authorize`.

If the hierarchy contains a cycle, for example:

```text
Organization A → Organization B → Organization A
```

transitive propagation throws `ScopeCycleDetected`.

The cycle is not silently converted to `false`, because it represents a configuration error that must be fixed.

### Scope changes and cache

Authorization projections are stored per subject. If the active organization is changed outside Laravel Authorization, invalidate the
subject's projection:

```php
use Vaened\Sentinel\Cache\AuthorizationCacheStore;

app(AuthorizationCacheStore::class)->forget($user);
```

Operations executed through Sentinel manage the corresponding invalidation automatically.

## Advanced usage

The default setup is documented in [Using the direct model API](#using-the-direct-model-api) and [Laravel Gate](#laravel-gate). This
section covers the less common case where authorization is integrated
manually through Sentinel's `Subject` contract.

### Keeping authorization out of the model

Use this mode when you want to keep authorization operations out of your
aggregate or application model. The model can still be a normal Eloquent
model; it only needs to implement Sentinel's `Subject` contract and expose its
identifier:

```php
use Illuminate\Database\Eloquent\Model;
use Vaened\Sentinel\Identifier;
use Vaened\Sentinel\Subject;

final class User extends Model implements Subject
{
    public function id(): int|string|Identifier
    {
        return $this->getKey();
    }

    public function scope(): Subject|null
    {
        return null;
    }
}
```

If the identifier is a value object, that value object must implement
Sentinel's [`Identifier`](https://github.com/vaened/php-sentinel/blob/master/src/Identifier.php)
contract. No other authorization methods are required on the model.

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
> model, create a custom trait based on [`Authorize`](src/Authorize.php) and
> keep only the methods you need, such as `grant`, `deny`, and `revoke`.

### Purging subject assignments

For exceptional cleanup workflows, remove every role assignment, direct
permission, and explicit denial from a subject without deleting the subject or
the global role and permission definitions:

```php
use Vaened\Authorization\Facades\Revoker;

Revoker::purge($user);
```

`purge()` is intentionally not part of the `Authorizable` contract or the
`Authorize` trait because it is an aggregate cleanup operation rather than a
regular model assignment operation.

Do not combine [`Abilities`](src/Abilities.php) with Laravel's native
`Authorizable` trait. Both define `can` and `cannot`, but with incompatible
signatures and different semantics. Use Laravel's native API on an
`Authenticatable` model, or use `Abilities` on a plain Eloquent model when you
want Sentinel's direct permission and role checks.

## Errors

Adapter-specific errors extend Sentinel’s base [
`AuthorizationError`](https://github.com/vaened/php-sentinel/blob/master/src/Errors/AuthorizationError.php).

Middleware authorization failures continue to use Laravel’s own `AuthorizationException`.

## Development

```bash
make composer-install
make test
```

## Additional documentation

You can find more details in the source code as well as in the tests located in [`tests/`](tests).

The tests cover different usage scenarios and can serve as additional reference for understanding the library’s behavior.
