<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Exceptions;

use Illuminate\Database\Eloquent\Model;

class UnauthorizedOwnerException extends UnauthorizedException implements AuthorizationException
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
