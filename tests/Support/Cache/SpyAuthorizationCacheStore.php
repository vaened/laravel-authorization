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

use Vaened\Sentinel\Cache\AuthorizationCacheStore;
use Vaened\Sentinel\Identifiers;
use Vaened\Sentinel\Projection\SubjectAuthorizationProjection;
use Vaened\Sentinel\Subject;

final class SpyAuthorizationCacheStore implements AuthorizationCacheStore
{
    public int $getCalls        = 0;

    public int $putCalls        = 0;

    public int $forgetCalls     = 0;

    public int $invalidateCalls = 0;

    /** @var array<string, SubjectAuthorizationProjection> */
    private array $values = [];

    public function get(Subject $subject): ?SubjectAuthorizationProjection
    {
        $this->getCalls++;

        return $this->values[$this->keyOf($subject)] ?? null;
    }

    public function put(Subject $subject, SubjectAuthorizationProjection $projection): void
    {
        $this->putCalls++;
        $this->values[$this->keyOf($subject)] = $projection;
    }

    public function forget(Subject $subject): void
    {
        $this->forgetCalls++;
        unset($this->values[$this->keyOf($subject)]);
    }

    public function invalidate(): void
    {
        $this->invalidateCalls++;
        $this->values = [];
    }

    public function currentVersion(): int
    {
        return 1;
    }

    public function keyOf(Subject $subject): string
    {
        return sprintf('subject:%s', Identifiers::value($subject->id()));
    }
}
