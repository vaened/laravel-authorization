<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Cache;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository as LaravelRepository;
use Vaened\Authorization\Configuration\Caching;
use Vaened\Authorization\SubjectType;
use Vaened\Sentinel\Cache\AuthorizationCacheStore;
use Vaened\Sentinel\Identifiers;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;
use Vaened\Sentinel\Subject;

/**
 * Laravel-native implementation of {@see AuthorizationCacheStore}.
 *
 * Detects whether the underlying Laravel cache store supports tags. When it does,
 * uses tags to perform real forget/invalidate (no orphaned projections). When it
 * does not, falls back to the PSR-16 versioning strategy: bump a global counter
 * so old namespaces become unreachable until the TTL expires them.
 *
 * Reads {@see Caching::prefix()} and the configured TTL directly from the
 * Laravel authorization configuration.
 */
final readonly class LaravelAuthorizationCacheStore implements AuthorizationCacheStore
{
    private const int VERSION_LOCK_TTL = 10;

    private const int VERSION_LOCK_WAIT = 5;

    private bool $taggable;

    public function __construct(
        private LaravelRepository $cache,
    )
    {
        $this->taggable = $this->cache->getStore() instanceof TaggableStore;
    }

    public function get(Subject $subject): SubjectAuthorizationProjection|null
    {
        $value = $this->resolveStore()->get($this->keyOf($subject));

        if (!is_array($value)) {
            return null;
        }

        return SubjectAuthorizationProjection::fromArray($value);
    }

    public function put(Subject $subject, SubjectAuthorizationProjection $projection): void
    {
        $this->resolveStore()->put(
            $this->keyOf($subject),
            $projection->toArray(),
            Caching::ttl($this->taggable),
        );
    }

    public function forget(Subject $subject): void
    {
        $this->resolveStore()->forget($this->keyOf($subject));
    }

    public function invalidate(): void
    {
        if ($this->taggable) {
            $this->resolveStore()->flush();
            return;
        }

        $store = $this->cache->getStore();

        if (!$store instanceof LockProvider) {
            $this->incrementVersion();
            return;
        }

        $store->lock($this->versionKey() . ':lock', self::VERSION_LOCK_TTL)
              ->block(self::VERSION_LOCK_WAIT, fn() => $this->incrementVersion());
    }

    public function currentVersion(): int
    {
        if ($this->taggable) {
            return 1;
        }

        $value = $this->cache->get($this->versionKey());

        if (is_int($value) && $value > 0) {
            return $value;
        }

        $value = random_int(1, PHP_INT_MAX - 1);
        $this->cache->forever($this->versionKey(), $value);

        return $value;
    }

    public function keyOf(Subject $subject): string
    {
        if ($this->taggable) {
            return sprintf(
                'subject:%s:%s:projection',
                SubjectType::resolve($subject),
                Identifiers::value($subject->id()),
            );
        }

        return sprintf(
            '%s:v%s:subject:%s:%s:projection',
            Caching::prefix(),
            $this->currentVersion(),
            SubjectType::resolve($subject),
            Identifiers::value($subject->id()),
        );
    }

    private function resolveStore(): LaravelRepository
    {
        if ($this->taggable) {
            return $this->cache->tags([Caching::prefix()]);
        }

        return $this->cache;
    }

    private function versionKey(): string
    {
        return sprintf('%s:version', Caching::prefix());
    }

    private function incrementVersion(): void
    {
        $this->cache->forever($this->versionKey(), $this->currentVersion() + 1);
    }
}
