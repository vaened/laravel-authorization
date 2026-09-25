<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Support\Cache;

use Illuminate\Contracts\Cache\Lock;

final readonly class BlockingLock implements Lock
{
    public function __construct(
        private Lock              $lock,
        private BlockingFileStore $store,
    )
    {
    }

    public function get($callback = null): mixed
    {
        return $this->store->runWhileLocked(fn() => $this->lock->get($callback));
    }

    public function block($seconds, $callback = null): mixed
    {
        return $this->store->runWhileLocked(fn() => $this->lock->block($seconds, $callback));
    }

    public function release(): bool
    {
        return $this->lock->release();
    }

    public function owner(): string
    {
        return $this->lock->owner();
    }

    public function forceRelease(): void
    {
        $this->lock->forceRelease();
    }
}
