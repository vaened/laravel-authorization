<?php
/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

class Determiner
{
    public static function listenUnauthorizedOwnerEventForLogger(): bool
    {
        return self::get('listeners.unauthorized-owner-logger', true);
    }

    public static function applyFormatToSecretName(): bool
    {
        return self::get('format-secret-name', true);
    }

    private static function get(string $key, bool $default = false): bool
    {
        return (bool) config("authorization.{$key}", $default);
    }
}
