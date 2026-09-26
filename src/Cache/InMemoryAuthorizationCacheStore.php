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

use Vaened\Authorization\SubjectType;
use Vaened\Sentinel\Cache\AuthorizationCacheStore;
use Vaened\Sentinel\Identifiers;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;
use Vaened\Sentinel\Subject;

/**
 * Keeps authorization projections in memory for the lifetime of this instance.
 *
 * This decorator does not define the instance lifetime. Its consumer must decide
 * whether it should live for a request, job, worker, or another execution scope.
 */
final class InMemoryAuthorizationCacheStore implements AuthorizationCacheStore
{
    /** @var array<string, SubjectAuthorizationProjection|null> */
    private array $projections = [];

    public function __construct(
        private readonly AuthorizationCacheStore $store,
    )
    {
    }

    public function get(Subject $subject): ?SubjectAuthorizationProjection
    {
        $key = $this->memoryKey($subject);

        if (array_key_exists($key, $this->projections)) {
            return $this->projections[$key];
        }

        return $this->projections[$key] = $this->store->get($subject);
    }

    public function put(Subject $subject, SubjectAuthorizationProjection $projection): void
    {
        $this->store->put($subject, $projection);
        $this->projections[$this->memoryKey($subject)] = $projection;
    }

    public function forget(Subject $subject): void
    {
        $this->store->forget($subject);
        unset($this->projections[$this->memoryKey($subject)]);
    }

    public function invalidate(): void
    {
        $this->store->invalidate();
        $this->projections = [];
    }

    public function currentVersion(): int
    {
        return $this->store->currentVersion();
    }

    public function keyOf(Subject $subject): string
    {
        return $this->store->keyOf($subject);
    }

    private function memoryKey(Subject $subject): string
    {
        return sprintf(
            '%s:%s',
            SubjectType::resolve($subject),
            Identifiers::value($subject->id()),
        );
    }
}
