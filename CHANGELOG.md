# Changelog

All notable changes to `laravel-authorization` will be documented in this file

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
