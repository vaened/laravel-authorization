<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Configuration;

final class Caching
{
    public static function store(): string|null
    {
        return config('authorization.cache.store');
    }

    public static function prefix(): string
    {
        return (string) config('authorization.cache.prefix', 'authorization');
    }

    public static function ttl(): int|null
    {
        $ttl = config('authorization.cache.ttl');

        return null === $ttl ? null : (int) $ttl;
    }
}
