<?php

declare(strict_types=1);

/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Illuminate\Database\Eloquent\Model;

class UnauthorizedOwnerException extends UnauthorizedException
{
    public function __construct(Model $model, array $headers = array())
    {
        parent::__construct($this->makeMessage($model), $headers);
    }

    private function makeMessage(Model $model): string
    {
        return "{$model->getMorphClass()} with key {$model->getKey()} is not authorized";
    }
}
