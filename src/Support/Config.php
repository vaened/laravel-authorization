<?php
/**
 * Created on 20/02/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

class Config
{
    public static function getDriver(): string
    {
        return self::getConfig('driver', 'database');
    }

    private static function getConfig(string $key, string $default = null): string
    {
        return config("authorization.{$key}", $default);
    }
}
