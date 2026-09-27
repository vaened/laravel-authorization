<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Cache;

use Illuminate\Database\Connection;
use Vaened\Sentinel\Cache\AuthorizationCacheStore;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;
use Vaened\Sentinel\Subject;

/**
 * Prevents authorization cache mutations from being published before commit.
 *
 * Cache mutations are deferred until the current transaction commits.
 */
final readonly class TransactionAwareAuthorizationCacheStore implements AuthorizationCacheStore
{
    public function __construct(
        private AuthorizationCacheStore $store,
        private Connection              $connection,
    )
    {
    }

    public function get(Subject $subject): SubjectAuthorizationProjection|null
    {
        return $this->store->get($subject);
    }

    public function put(Subject $subject, SubjectAuthorizationProjection $projection): void
    {
        if ($this->inTransaction()) {
            return;
        }

        $this->store->put($subject, $projection);
    }

    public function forget(Subject $subject): void
    {
        $this->store->forget($subject);

        if ($this->inTransaction()) {
            $this->connection->afterCommit(fn() => $this->store->forget($subject));
        }
    }

    public function invalidate(): void
    {
        $this->store->invalidate();

        if ($this->inTransaction()) {
            $this->connection->afterCommit(fn() => $this->store->invalidate());
        }
    }

    public function currentVersion(): int
    {
        return $this->store->currentVersion();
    }

    public function keyOf(Subject $subject): string
    {
        return $this->store->keyOf($subject);
    }

    private function inTransaction(): bool
    {
        return $this->connection->transactionLevel() > 0;
    }
}
