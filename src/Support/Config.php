<?php
/**
 * Created on 20/02/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

class Config
{
    public static function getDriver(): string
    {
        return self::get('driver', 'database');
    }

    public static function listenUnauthorizedOwnerEventForLogger(): bool
    {
        return self::get('listeners.unauthorized-owner-logger', true);
    }

    public static function get(string $key, string $default = null): string
    {
        return config("authorization.{$key}", $default);
    }
}
