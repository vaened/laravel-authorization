<?php
/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Enea\Authorization\Contracts\Grantable;

abstract class UncompletedOperationException extends AuthorizationException
{
    private $grantable;

    public function __construct(Grantable $grantable)
    {
        parent::__construct($this->makeMessage($grantable));
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
