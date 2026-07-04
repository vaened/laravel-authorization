<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Exceptions;

use Vaened\Authorization\Contracts\Grantable;
use RuntimeException;
use Throwable;

abstract class UncompletedOperationException extends RuntimeException implements AuthorizationException
{
    private $grantable;

    public function __construct(Grantable $grantable, Throwable $previous = null)
    {
        parent::__construct($this->makeMessage($grantable), 0, $previous);
        $this->grantable = $grantable;
    }

    abstract protected function getOperationName(): string;

    public function getGrantable(): Grantable
    {
        return $this->grantable;
    }

    private function makeMessage(Grantable $grantable): string
    {
        return "The authorization '{$grantable->getSecretName()}' could not be {$this->getOperationName()}";
    }
}
