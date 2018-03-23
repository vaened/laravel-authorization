<?php
/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

class Listeners
{
    public static function listenUnauthorizedOwnerEventForLogger(): bool
    {
        return self::get('listeners.unauthorized-owner-logger', true);
    }

    private static function get(string $key, bool $default = false): bool
    {
        return (bool) config("authorization.{$key}", $default);
    }
}
