<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Unit\Cache;

use LogicException;
use Vaened\Authorization\Cache\InMemoryAuthorizationCacheStore;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Authorization\Tests\Support\Cache\SpyAuthorizationCacheStore;
use Vaened\Authorization\Tests\TestCase;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;

final class InMemoryAuthorizationCacheStoreTest extends TestCase
{
    public function test_it_reads_the_persistent_store_only_once_per_subject(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $store      = new InMemoryAuthorizationCacheStore($persistent);
        $subject    = new TestSubject(1);

        $store->get($subject);
        $store->get($subject);

        self::assertSame(1, $persistent->getCalls);
    }

    public function test_it_does_not_ask_the_persistent_store_for_a_memory_key(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $store      = new InMemoryAuthorizationCacheStore($persistent);
        $subject    = new TestSubject(1);

        $store->get($subject);
        $store->get($subject);

        self::assertSame(0, $persistent->keyCalls);
    }

    public function test_put_updates_the_memory_layer(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $store      = new InMemoryAuthorizationCacheStore($persistent);
        $subject    = new TestSubject(1);
        $projection = self::projection();

        $store->get($subject);
        $store->put($subject, $projection);

        self::assertSame($projection, $store->get($subject));
        self::assertSame(1, $persistent->getCalls);
    }

    public function test_forget_clears_the_memory_layer(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $store      = new InMemoryAuthorizationCacheStore($persistent);
        $subject    = new TestSubject(1);

        $persistent->put($subject, self::projection());
        $store->get($subject);
        $store->forget($subject);
        $store->get($subject);

        self::assertSame(2, $persistent->getCalls);
        self::assertSame(1, $persistent->forgetCalls);
    }

    public function test_invalidate_clears_the_memory_layer(): void
    {
        $persistent = new SpyAuthorizationCacheStore();
        $store      = new InMemoryAuthorizationCacheStore($persistent);
        $subject    = new TestSubject(1);

        $persistent->put($subject, self::projection());
        $store->get($subject);
        $store->invalidate();
        $store->get($subject);

        self::assertSame(2, $persistent->getCalls);
        self::assertSame(1, $persistent->invalidateCalls);
    }

    private static function projection(): SubjectAuthorizationProjection
    {
        return SubjectAuthorizationProjection::fromArray([
            'roles'       => ['admin'],
            'permissions' => ['users.read' => 1],
        ]) ?? throw new LogicException('The projection payload must be valid.');
    }
}
