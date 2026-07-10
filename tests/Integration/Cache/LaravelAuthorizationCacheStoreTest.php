<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Cache;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\DatabaseStore;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\DB;
use Vaened\Authorization\Cache\LaravelAuthorizationCacheStore;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;

/**
 * Covers both branches of {@see LaravelAuthorizationCacheStore}:
 *
 *  - Taggable mode: when the underlying store extends {@see TaggableStore}
 *    (Redis, Memcached, APC, Array), tags are used for real invalidation.
 *  - Versioned mode: when the underlying store does NOT support tags
 *    (file, database), a versioned namespace is used as fallback.
 */
final class LaravelAuthorizationCacheStoreTest extends DatabaseTestCase
{
    public function test_in_taggable_mode_key_of_returns_a_clean_key_without_prefix_or_version(): void
    {
        $store   = $this->createTaggableStore();
        $subject = new TestSubject(7);

        $key = $store->keyOf($subject);

        self::assertSame(sprintf('subject:%s:%s:projection', TestSubject::class, $subject->id()), $key);
    }

    public function test_in_taggable_mode_current_version_is_always_one(): void
    {
        $store = $this->createTaggableStore();

        self::assertSame(1, $store->currentVersion());
    }

    public function test_in_taggable_mode_put_then_get_round_trips_the_projection(): void
    {
        $store    = $this->createTaggableStore();
        $subject  = new TestSubject(1);
        $expected = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject, $expected);

        $actual = $store->get($subject);
        self::assertNotNull($actual);
        self::assertSame(['admin'], $actual->roles()->codes());
        self::assertSame(['users.read' => 2], $actual->toArray()['permissions']);
    }

    public function test_in_taggable_mode_get_returns_null_when_no_projection_exists(): void
    {
        $store   = $this->createTaggableStore();
        $subject = new TestSubject(99);

        self::assertNull($store->get($subject));
    }

    public function test_in_taggable_mode_forget_removes_the_specific_subject_projection(): void
    {
        $store      = $this->createTaggableStore();
        $subject1   = new TestSubject(1);
        $subject2   = new TestSubject(2);
        $projection = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject1, $projection);
        $store->put($subject2, $projection);
        $store->forget($subject1);

        self::assertNull($store->get($subject1));
        self::assertNotNull($store->get($subject2));
    }

    public function test_in_taggable_mode_invalidate_flushes_all_projections(): void
    {
        $store      = $this->createTaggableStore();
        $subject    = new TestSubject(1);
        $projection = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject, $projection);
        self::assertNotNull($store->get($subject));

        $store->invalidate();

        self::assertNull(
            $store->get($subject),
            'Taggable invalidate() must call flush() so subsequent reads return null',
        );
    }

    public function test_in_versioned_mode_key_of_returns_namespaced_versioned_key(): void
    {
        $store   = $this->createNonTaggableStore();
        $subject = new TestSubject(7);

        $key = $store->keyOf($subject);

        self::assertStringContainsString('authorization:v1:', $key);
        self::assertStringContainsString('subject:', $key);
        self::assertStringContainsString(':7:projection', $key);
    }

    public function test_in_versioned_mode_current_version_reads_from_cache_and_defaults_to_one(): void
    {
        $store = $this->createNonTaggableStore();

        self::assertSame(1, $store->currentVersion());
    }

    public function test_in_versioned_mode_put_then_get_round_trips_the_projection(): void
    {
        $store    = $this->createNonTaggableStore();
        $subject  = new TestSubject(1);
        $expected = self::projection(['cashier'], ['users.read' => 1]);

        $store->put($subject, $expected);

        $actual = $store->get($subject);
        self::assertNotNull($actual);
        self::assertSame(['cashier'], $actual->roles()->codes());
        self::assertSame(['users.read' => 1], $actual->toArray()['permissions']);
    }

    public function test_in_versioned_mode_forget_removes_the_subject_projection(): void
    {
        $store      = $this->createNonTaggableStore();
        $subject    = new TestSubject(1);
        $projection = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject, $projection);
        $store->forget($subject);

        self::assertNull($store->get($subject));
    }

    public function test_in_versioned_mode_invalidate_bumps_version_so_old_keys_become_orphaned(): void
    {
        $store      = $this->createNonTaggableStore();
        $subject    = new TestSubject(1);
        $projection = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject, $projection);
        self::assertSame(1, $store->currentVersion());

        $store->invalidate();

        self::assertSame(2, $store->currentVersion());
        self::assertNull(
            $store->get($subject),
            'After bump, the old version is unreachable so get() must return null',
        );
    }

    public function test_in_versioned_mode_new_put_uses_the_bumped_version(): void
    {
        $store      = $this->createNonTaggableStore();
        $subject    = new TestSubject(1);
        $projection = self::projection(['admin'], ['users.read' => 2]);

        $store->put($subject, $projection);
        $store->invalidate();

        $store->put($subject, $projection);

        self::assertSame(2, $store->currentVersion());
    }

    private function createTaggableStore(): LaravelAuthorizationCacheStore
    {
        return new LaravelAuthorizationCacheStore(new Repository(new ArrayStore()));
    }

    private function createNonTaggableStore(): LaravelAuthorizationCacheStore
    {
        return new LaravelAuthorizationCacheStore(
            new Repository(new DatabaseStore(
                DB::connection(),
                'cache',
                '',
            )),
        );
    }

    private static function projection(array $roles, array $permissions): SubjectAuthorizationProjection
    {
        return SubjectAuthorizationProjection::fromArray([
            'roles'       => $roles,
            'permissions' => $permissions,
        ]) ?? throw new \LogicException('The projection payload must be valid.');
    }
}
