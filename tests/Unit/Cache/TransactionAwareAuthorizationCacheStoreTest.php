<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Unit\Cache;

use Illuminate\Database\Connection;
use LogicException;
use Mockery;
use Vaened\Authorization\Cache\InMemoryAuthorizationCacheStore;
use Vaened\Authorization\Cache\TransactionAwareAuthorizationCacheStore;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Authorization\Tests\Support\Cache\SpyAuthorizationCacheStore;
use Vaened\Authorization\Tests\TestCase;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;

final class TransactionAwareAuthorizationCacheStoreTest extends TestCase
{
    public function test_it_does_not_store_a_projection_inside_a_transaction(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $memory     = new InMemoryAuthorizationCacheStore($persistent);
        $connection = Mockery::mock(Connection::class);

        $connection->allows('transactionLevel')->andReturn(1);
        $connection->shouldNotReceive('afterCommit');

        $store = new TransactionAwareAuthorizationCacheStore(
            $memory,
            $connection,
        );

        $subject    = new TestSubject(1);
        $projection = self::projection();
        $store->put($subject, $projection);

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($memory->get($subject));
    }

    public function test_a_rolled_back_transaction_does_not_publish_its_projection(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $memory     = new InMemoryAuthorizationCacheStore($persistent);
        $connection = Mockery::mock(Connection::class);

        $connection->allows('transactionLevel')->andReturn(1);
        $connection->shouldNotReceive('afterCommit');

        $store = new TransactionAwareAuthorizationCacheStore($memory, $connection);
        $store->put(new TestSubject(1), self::projection());

        self::assertSame(0, $persistent->putCalls);
        self::assertNull($memory->get(new TestSubject(1)));
    }

    public function test_it_delegates_immediately_without_a_transaction(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $connection = Mockery::mock(Connection::class);
        $connection->expects('transactionLevel')->andReturn(0);
        $connection->shouldNotReceive('afterCommit');

        $store = new TransactionAwareAuthorizationCacheStore(
            new InMemoryAuthorizationCacheStore($persistent),
            $connection,
        );

        $store->put(new TestSubject(1), self::projection());

        self::assertSame(1, $persistent->putCalls);
    }

    public function test_it_forgets_immediately_and_after_commit(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $memory     = new InMemoryAuthorizationCacheStore($persistent);
        $connection = Mockery::mock(Connection::class);
        $callback   = null;
        $subject    = new TestSubject(1);
        $projection = self::projection();

        $memory->put($subject, $projection);
        $connection->allows('transactionLevel')->andReturn(1);
        $connection->expects('afterCommit')
                   ->with(Mockery::type('callable'))
                   ->andReturnUsing(static function (callable $afterCommit) use (&$callback): void {
                       $callback = $afterCommit;
                   });

        $store = new TransactionAwareAuthorizationCacheStore($memory, $connection);
        $store->forget($subject);

        self::assertNull($memory->get($subject));
        self::assertSame(1, $persistent->forgetCalls);

        self::assertNotNull($callback);
        $callback();

        self::assertNull($memory->get($subject));
        self::assertSame(2, $persistent->forgetCalls);
    }

    public function test_it_invalidates_immediately_and_after_commit(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $memory     = new InMemoryAuthorizationCacheStore($persistent);
        $connection = Mockery::mock(Connection::class);
        $callback   = null;

        $memory->put(new TestSubject(1), self::projection());
        $memory->put(new TestSubject(2), self::projection());
        $connection->allows('transactionLevel')->andReturn(1);
        $connection->expects('afterCommit')
                   ->with(Mockery::type('callable'))
                   ->andReturnUsing(static function (callable $afterCommit) use (&$callback): void {
                       $callback = $afterCommit;
                   });

        $store = new TransactionAwareAuthorizationCacheStore($memory, $connection);
        $store->invalidate();

        self::assertNull($memory->get(new TestSubject(1)));
        self::assertNull($memory->get(new TestSubject(2)));
        self::assertSame(1, $persistent->invalidateCalls);

        self::assertNotNull($callback);
        $callback();

        self::assertNull($memory->get(new TestSubject(1)));
        self::assertNull($memory->get(new TestSubject(2)));
        self::assertSame(2, $persistent->invalidateCalls);
    }

    public function test_it_delegates_forget_and_invalidate_immediately_without_a_transaction(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $connection = Mockery::mock(Connection::class);
        $connection->allows('transactionLevel')->andReturn(0);
        $connection->shouldNotReceive('afterCommit');

        $store = new TransactionAwareAuthorizationCacheStore(
            new InMemoryAuthorizationCacheStore($persistent),
            $connection,
        );

        $store->forget(new TestSubject(1));
        $store->invalidate();

        self::assertSame(1, $persistent->forgetCalls);
        self::assertSame(1, $persistent->invalidateCalls);
    }

    private static function projection(): SubjectAuthorizationProjection
    {
        return SubjectAuthorizationProjection::fromArray([
            'roles'       => ['admin'],
            'permissions' => ['users.read' => 2],
        ]) ?? throw new LogicException('The projection payload must be valid.');
    }
}
