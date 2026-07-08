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

final class Middlewares
{
    public static function permissions(): string
    {
        return config('authorization.middlewares.permissions');
    }

    public static function roles(): string
    {
        return config('authorization.middlewares.roles');
    }
}
