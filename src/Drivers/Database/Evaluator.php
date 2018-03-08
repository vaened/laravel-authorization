<?php
/**
 * Created on 22/02/18 by enea dhack.
 */

namespace Enea\Authorization\Drivers\Database;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Evaluator
{
    protected function has(Builder $repository): Closure
    {
        return function (string $grantableName) use ($repository): bool {
            return ($this->equals($grantableName)($repository))->exists();
        };
    }

    protected function equals(string $grantableName): Closure
    {
        return function (Builder $repository) use ($grantableName): Builder {
            return $repository->limit(1)->where('secret_name', $grantableName);
        };
    }
}
