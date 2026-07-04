<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Drivers\Cache;

class CacheConfig
{
    public static function getExpirationTime(): int
    {
        return (int) self::get('expiration-time', '1440');
    }

    private static function get(string $key, string $default = null): string
    {
        return (string) config("authorization.drivers.cache.{$key}", $default);
    }
}
