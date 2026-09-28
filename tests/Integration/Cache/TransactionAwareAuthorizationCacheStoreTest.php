<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Cache;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Database\SQLiteConnection;
use LogicException;
use Mockery;
use PDO;
use Vaened\Authorization\Cache\TransactionAwareAuthorizationCacheStore;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Authorization\Tests\Support\Cache\SpyAuthorizationCacheStore;
use Vaened\Authorization\Tests\TestCase;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;

final class TransactionAwareAuthorizationCacheStoreTest extends TestCase
{
    public function test_a_transactional_put_is_not_published_after_the_transaction_commits(): void
    {
        $connection = $this->connection();
        $persistent = new SpyAuthorizationCacheStore();
        $store      = $this->store($persistent, $connection);
        $subject    = new TestSubject(1);

        $connection->beginTransaction();
        $store->put($subject, self::projection());

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($store->get($subject));

        $connection->commit();

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($store->get($subject));
    }

    public function test_a_real_rollback_discards_the_deferred_publication(): void
    {
        $connection = $this->connection();
        $persistent = new SpyAuthorizationCacheStore();
        $store      = $this->store($persistent, $connection);

        $connection->beginTransaction();
        $store->put(new TestSubject(1), self::projection());
        $connection->rollBack();

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($store->get(new TestSubject(1)));
    }

    public function test_a_transactional_put_is_not_published_after_commit(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $connection = Mockery::mock(Connection::class);
        $callback   = null;
        $subject    = new TestSubject(1);

        $connection->allows('transactionLevel')->andReturn(1);
        $connection->allows('afterCommit')
                   ->andReturnUsing(static function (callable $afterCommit) use (&$callback): void {
                       $callback = $afterCommit;
                   });

        $store = $this->store($persistent, $connection);
        $store->put($subject, self::projection());

        if (null !== $callback) {
            $callback();
        }

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($persistent->get($subject));
    }

    public function test_a_transactional_forget_runs_immediately_and_after_commit(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $connection = Mockery::mock(Connection::class);
        $callback   = null;
        $subject    = new TestSubject(1);

        $persistent->put($subject, self::projection());
        $connection->allows('transactionLevel')->andReturn(1);
        $connection->allows('afterCommit')
                   ->andReturnUsing(static function (callable $afterCommit) use (&$callback): void {
                       $callback = $afterCommit;
                   });

        $store = new TransactionAwareAuthorizationCacheStore($persistent, $connection);
        $store->forget($subject);

        self::assertNull($persistent->get($subject));
        self::assertSame(1, $persistent->forgetCalls);

        self::assertNotNull($callback);
        $callback();

        self::assertSame(2, $persistent->forgetCalls);
    }

    public function test_a_transactional_invalidate_runs_immediately_and_after_commit(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $connection = Mockery::mock(Connection::class);
        $callback   = null;
        $subject    = new TestSubject(1);

        $persistent->put($subject, self::projection());
        $connection->allows('transactionLevel')->andReturn(1);
        $connection->allows('afterCommit')
                   ->andReturnUsing(static function (callable $afterCommit) use (&$callback): void {
                       $callback = $afterCommit;
                   });

        $store = new TransactionAwareAuthorizationCacheStore($persistent, $connection);
        $store->invalidate();

        self::assertNull($persistent->get($subject));
        self::assertSame(1, $persistent->invalidateCalls);

        self::assertNotNull($callback);
        $callback();

        self::assertSame(2, $persistent->invalidateCalls);
    }

    public function test_a_nested_commit_waits_for_the_outermost_commit(): void
    {
        $connection = $this->connection();
        $persistent = new SpyAuthorizationCacheStore();
        $store      = $this->store($persistent, $connection);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $store->put(new TestSubject(1), self::projection());

        $connection->commit();
        self::assertSame(0, $persistent->putCalls);

        $connection->commit();
        self::assertSame(0, $persistent->putCalls);
    }

    public function test_a_nested_rollback_discards_the_deferred_publication(): void
    {
        $connection = $this->connection();
        $persistent = new SpyAuthorizationCacheStore();
        $store      = $this->store($persistent, $connection);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $store->put(new TestSubject(1), self::projection());

        $connection->rollBack();
        $connection->commit();

        self::assertSame(0, $persistent->putCalls);
    }

    private function connection(): SQLiteConnection
    {
        return new SQLiteConnection(new PDO('sqlite::memory:'))
            ->setTransactionManager(new DatabaseTransactionsManager());
    }

    private function store(
        SpyAuthorizationCacheStore $persistent,
        Connection                 $connection,
    ): TransactionAwareAuthorizationCacheStore
    {
        return new TransactionAwareAuthorizationCacheStore(
            $persistent,
            $connection,
        );
    }

    private static function projection(): SubjectAuthorizationProjection
    {
        return SubjectAuthorizationProjection::fromArray([
            'roles'       => ['admin'],
            'permissions' => ['users.read' => 2],
        ]) ?? throw new LogicException('The projection payload must be valid.');
    }
}
