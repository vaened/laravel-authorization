# Changelog

All notable changes to `laravel-authorization` will be documented in this file

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

### Added
- Everything, initial release
