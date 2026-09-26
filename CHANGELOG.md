# Changelog

All notable changes to `laravel-authorization` will be documented in this file

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
