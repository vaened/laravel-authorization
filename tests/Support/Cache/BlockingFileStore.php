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

use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;

final class BlockingFileStore extends FileStore
{
    private string $barrier;

    private bool   $lockHeld = false;

    public function __construct(
        Filesystem|string $filesOrDirectory,
        ?string           $directoryOrBarrier = null,
        ?int              $filePermission = null,
        array|bool|null   $serializableClasses = null,
    )
    {
        if ($filesOrDirectory instanceof Filesystem) {
            parent::__construct(
                $filesOrDirectory,
                (string)$directoryOrBarrier,
                $filePermission,
                $serializableClasses,
            );
            $this->barrier = '';

            return;
        }

        parent::__construct(new Filesystem(), (string)$filesOrDirectory);
        $this->barrier = (string)$directoryOrBarrier;
    }

    public function get($key)
    {
        if ($key === 'authorization:version' && '' !== $this->barrier && !$this->lockHeld) {
            touch($this->barrier . '/' . getmypid());

            $deadline = microtime(true) + 5;

            while (count(glob($this->barrier . '/*')) < 2 && microtime(true) < $deadline) {
                usleep(1_000);
            }

            if (count(glob($this->barrier . '/*')) < 2) {
                throw new RuntimeException('The cache invalidation barrier timed out.');
            }
        }

        return parent::get($key);
    }

    public function lock($name, $seconds = 0, $owner = null): Lock
    {
        return new BlockingLock(parent::lock($name, $seconds, $owner), $this);
    }

    public function runWhileLocked(callable $callback): mixed
    {
        $this->lockHeld = true;

        try {
            return $callback();
        } finally {
            $this->lockHeld = false;
        }
    }
}
