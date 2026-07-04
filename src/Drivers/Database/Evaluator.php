<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Drivers\Database;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Evaluator
{
    protected function has(Builder $repository): Closure
    {
        return function (array $grantableNames) use ($repository): bool {
            return ($this->same($grantableNames)($repository))->exists();
        };
    }

    protected function same(array $grantableNames): Closure
    {
        return function (Builder $repository) use ($grantableNames): Builder {
            return $repository->limit(1)->whereIn('secret_name', $grantableNames);
        };
    }
}
