<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Drivers\Cache\Repositories;

use Closure;
use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Drivers\Cache\CacheConfig;
use Enea\Authorization\Drivers\Cache\KeyBuilder;
use Enea\Authorization\Drivers\Cache\Struct;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Collection;

abstract class Repository
{
    private $cache;

    private $key;

    public function __construct(Cache $cache, KeyBuilder $key)
    {
        $this->cache = $cache;
        $this->key = $key;
    }

    abstract public static function getSuffix(): string;

    public function forget(GrantableOwner $owner): void
    {
        $this->cache->forget($this->keyFor($owner));
    }

    protected function remember(GrantableOwner $owner, Closure $closure): Collection
    {
        $minutes = CacheConfig::getExpirationTime();
        return $this->cache->remember($this->keyFor($owner), $minutes, $closure);
    }

    protected function keyFor(GrantableOwner $owner): string
    {
        return "{$this->key->make($owner)}.{$this->getSuffix()}";
    }

    protected function parse(): Closure
    {
        return function (Grantable $grantable): Struct {
            return Struct::make($grantable->getSecretName());
        };
    }
}
