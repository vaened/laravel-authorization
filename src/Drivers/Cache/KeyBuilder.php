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

use Vaened\Authorization\Contracts\Owner;

class KeyBuilder
{
    public const HASH = 'crc32b';

    public function make(Owner $owner): string
    {
        return "{$this->prefix($owner)}.{$owner->getIdentificationKey()}";
    }

    private function prefix(Owner $owner): string
    {
        return hash(self::HASH, get_class($owner));
    }
}
