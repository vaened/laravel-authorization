# Changelog

All notable changes to `laravel-authorization` will be documented in this file

## V6.0.0 - 2026-09-27

### Upgrade notes

- Update the roles table manually; the package migration was changed in place
  and is not run again on existing installations:
    - add nullable `scope_type` (string) and `scope_id` columns, with the same
      key type as your scope models, and an index on both;
    - replace the unique index on `code` with a unique index on
      `code`, `scope_type`, and `scope_id`.

  Existing roles keep a `null` scope and remain global roles; assignments are
  not modified.
- Subjects that implement Sentinel's `Subject` contract without the `Authorize`
  trait must add `scope(): Subject|null`. Returning `null` keeps the `V5`
  behavior.
- `RoleRegistry::find()` and `RoleRegistry::lookup()` now require a scope as
  their first argument. Pass `null` for global roles, for example
  `$roles->find(null, 'admin')`.
- Custom repository implementations must follow the PHP Sentinel `0.10`
  contracts: `RoleRepository::lookup()` receives the scope first,
  `RoleRepository::create()` accepts an optional scope, `RoleRepository` adds
  `match()`, and `RolePermissionRepository` adds `grants()`. Applications that
  construct Sentinel's `Granter` or `CachedSubjectRoleRepository` manually must
  follow their new constructor signatures.
- Published configuration files are merged only at the top level, so a
  published `cache` section keeps `'ttl' => null` and continues to store
  projections permanently on stores with tag support. Set `'ttl' => 43_200` to
  adopt the new default. The new `subject` and `propagation` sections use their
  defaults when they are missing from the published file.
- The cached projection format did not change, so invalidating the cache is not
  required. If you adopt a TTL, run `php artisan authorization:cache:invalidate`
  so projections cached permanently under `V5` are rebuilt with it.
- Route middleware now resolves the subject through the configured resolver.
  With the default resolver, an authenticated user that does not implement
  `Subject` raises `InvalidAuthorizationSubject` instead of an
  `AuthorizationException` (403).

### Added

- Added scoped authorization. Subjects expose `scope()`, roles can belong to a
  scope through `RoleRegistry::create($code, $name, $description, $scope)`,
  and permission checks evaluate the subject together with its scope chain.
- Added the `authorization.propagation` configuration.
  `TransitiveScopePropagationPolicy` (default) evaluates the direct scope and
  all of its ancestors; `DirectScopePropagationPolicy` evaluates only the
  immediate scope.
- Added the `authorization.subject.resolver` configuration and the
  `AuthorizationSubjectResolver` contract, so the Gate integration and route
  middleware can authorize a subject other than the authenticated user, such
  as an organization membership. The default `AuthenticatedUserSubjectResolver`
  keeps the `V5` behavior. Resolutions are cached per user for the current
  request or job.
- Added `AuthorizationSubjectProvider::current()` to retrieve the resolved
  subject of the current request, and the `AuthorizationSubjectNotFound` error.
- Added the `InvalidAuthorizationSubject` error.
- Added `Role::scope()` and the `Role::context()` morph relation. A scope that
  does not implement `Subject` raises `InvalidAuthorizationScope`.
- Added a default `scope()` implementation, returning `null`, to the
  `Authorize` trait.
- Added `TransactionAwareAuthorizationCacheStore`, registered by default. It
  observes transactions on the default database connection.
- Added `EloquentRoleRepository::match()` and
  `EloquentRolePermissionRepository::grants()`, which loads the permissions of
  several roles in one query.
- Documented multitenancy: one organization per user, memberships as subjects,
  custom subject resolvers, roles and scopes, and scope propagation.

### Changed

- Updated PHP Sentinel to `^0.10`.
- Roles now store an optional `scope_type` and `scope_id`. Role codes are
  unique per scope instead of globally.
- Global and scoped roles share one code namespace: creating a scoped role with
  the code of a global role, or the reverse, raises `RoleAlreadyExists`.
- Granting a role or permission now validates scope compatibility before
  writing. A rejected request raises `InvalidAuthorization` and stores none of
  the requested assignments.
- The default `authorization.cache.ttl` is now `43_200` seconds (twelve hours)
  instead of `null`.
- The Gate integration evaluates the subject returned by the configured
  resolver. It still abstains when no subject is resolved, including for users
  that do not implement `Subject`.
- `authorization:sync` now creates, updates, and prunes only global roles;
  scoped roles are never pruned.

### Fixed

- Authorization projections read while a database transaction is open are no
  longer written to the shared cache. Previously, a projection built from
  uncommitted data could be served to other requests, including after a
  rollback.
- Cache invalidations issued inside a transaction are now applied immediately
  and again after the transaction commits, so concurrent requests can no longer
  keep a projection from before the commit.

## V5.0.0 - 2026-09-26

### Upgrade notes

- Run `php artisan authorization:cache:invalidate` during deployment. The cached
  projection format changed with PHP Sentinel `0.8`, and cache keys now use the
  subject's morph type.
- Replace the `Authorizations` trait with `Authorize`. Models that extend
  Eloquent's base `Model` and need Sentinel permission and role checks should
  also use `Abilities`. Models that extend Laravel's `Authenticatable` keep
  Laravel's native `can` and `cannot` methods.
- Remove any handling of `UnsupportedSubject`; subjects no longer need to be
  Eloquent models.
- Authorization services are now bound as `scoped` instances. Do not inject them
  into your own singletons or long-lived services.

### Added

- Added `Revoker::purge()` to remove every role assignment, direct permission,
  and explicit denial from a subject while keeping the global role and
  permission definitions.
- Added a request-scoped in-memory projection layer, so each subject's
  projection is read from the cache store at most once per request or job.
- Added support for non-Eloquent subjects, such as Doctrine entities, in the
  Gate integration, route middleware, and repositories.
- Added the `Abilities` trait with `can`, `cannot`, `actsAs`, and `actsNotAs`
  for plain Eloquent models.
- Added lock-protected version increments for cache stores without tag support.
- Documented single-subject cache invalidation through
  `AuthorizationCacheStore::forget()`.

### Changed

- Split the `Authorizations` trait into `Authorize` (`grant`, `deny`, `revoke`)
  and `Abilities` (`can`, `cannot`, `actsAs`, `actsNotAs`).
- Removed `can`, `cannot`, `actsAs`, and `actsNotAs` from the `Authorizable`
  contract.
- Route middleware now authorizes any PHP Sentinel `Subject`.
- `authorization:sync` now invalidates the authorization cache after its
  transaction commits, and only when the synchronization applied changes.
- Repository bindings are now registered during the service provider's
  `register` phase.
- Updated PHP Sentinel to `^0.9`.
- Changed the Composer package type to `library` and removed the unused
  `psr/log` dependency.

### Fixed

- Fixed the default README setup, which triggered a fatal error on models that
  extend Laravel's `Authenticatable`.
- Inherited permission checks no longer query the database on every call.
- Cache keys and persisted assignments now resolve the subject type the same
  way, preventing stale grants when several classes share a morph type.
- Losing the cache version key no longer revives projections from a previous
  cache namespace.
- Concurrent cache invalidations are no longer lost in versioned cache mode.
- Subjects with a magic `__call` method no longer persist an invalid
  `authorizable_type`.
- Requests running during `authorization:sync` can no longer cache
  pre-synchronization projections indefinitely.

### Removed

- Removed the `Authorizations` trait.
- Removed the `UnsupportedSubject` error.
-

## V4.3.1 - 2026-08-24

### Fixed

- Updated PHP Sentinel to `0.7.1`, including fixes for inherited permission
  handling and stale cache namespace reuse.

## V4.3.0 - 2026-07-20

### Added

- Added independent control over permission and role synchronization. Set a
  section to `false` or `null` to manage it externally, such as through an
  administration panel.

### Changed

- Updated `authorization:sync --prune` to skip disabled sections, preventing
  externally managed roles or permissions from being removed.

## V4.2.0 - 2026-07-20

### Added

- Added Laravel 13 support while retaining compatibility with Laravel 12.
- Added the authorization definitions file and `authorization:sync` command for
  declaratively creating, updating, and reconciling application roles and
  permissions, with optional pruning of unused entries.
- Added the interactive `authorization:install` command for publishing the
  package configuration, authorization definitions, and database migrations.

## V4.1.1 - 2026-07-11

### Fixed

- Corrected migration publishing so the migration is copied to Laravel's
  `database/migrations` directory instead of the application root.
- Added the timestamp prefix required for Laravel to refresh the migration
  timestamp when publishing the package migration.

## V4.1.0 - 2026-07-11

### Added

- Added configurable integration with Laravel's authorization Gate.
- Added support for Laravel's native Gate APIs, including `can` middleware,
  when Gate integration is enabled.
- Documented the default package integration and the alternative Laravel-native
  authorization model.

### Changed

- Enabled the `after` Gate strategy by default, allowing Sentinel to act as a
  fallback after Laravel's own Gates and Policies.
- Added `before` and `null` configuration options for applications that need
  Sentinel to take precedence or want to disable Gate integration.
- Expanded the Gate integration test coverage for `allows`, `check`, `any`, and
  `none`, including their iterable authorization semantics.

### Fixed

- Corrected the migration publishing path so the package publishes its
  migration file with Laravel's expected migration name.

## V4.0.0 - 2026-07-10

### Changed

- Rebuilt the package for Laravel 12 and PHP 8.4 around the current PHP Sentinel authorization model.
- Replaced the previous package internals with Eloquent-backed role, permission, role-permission, subject-role, and subject-permission
  repositories.
- Made application user models authorizable through the `Authorizable` contract and `Authorizations` trait.
- Reworked configuration, migrations, middleware aliases, default models, and service provider bindings for the new package architecture.

### Added

- Support for roles, direct permission grants, explicit permission denials, and inherited permissions from roles.
- Laravel-native authorization caching with tag-aware invalidation and a bounded fallback for cache stores without tags.
- `authorization:cache:invalidate` Artisan command for global authorization-cache invalidation.
- Role and permission catalog management through PHP Sentinel registries.

### Breaking

- This is a complete rewrite and is not backward compatible with earlier releases.
- Applications must migrate to the new database schema, configuration structure, authorizable model contract, and PHP Sentinel-based APIs.

## V2.0.0 - 2022-07-11

### Added

- Support for Laravel 9
- Support for php 8.1

## V1.1.0 - 2018-04-21

### Added

- Add facade `Authenticated` to facilitate the use of the checks of roles and permissions
- Enable automatic middleware configuration

## V1.0.0 - 2018-04-15

### Added

- Now it is allowed to deny a permission through the methods `deny` and `denyMultiple`
- The event `Denied` was added

### Changed

- The `GrantableOwner` was renamed to simply `Owner`
- The `denied` column was added to the `user_permissions` table

## V0.2.1 - 2018-04-08

### Fixed

- The `AuthorizationException` exception now extends from Throwable

## V0.2.0 - 2018-04-02

### Changed

- The struct for authorizations was renamed from `Struct` to `Authorization`

## V0.1.1 - 2018-03-30

### Added

* Added Blade directives
    - `@authenticatedCan`
    - `@authenticatedCannot`
    - `@authenticatedIs`
    - `@authenticatedIsnt`
* Added Helpers class

## V0.1.0 - 2018-03-29

### Added

- Cache driver

### Changed

- Default driver is now cache

## V0.0.1 - 2018-03-25