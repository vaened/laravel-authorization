<?php

declare(strict_types=1);

/**
 * Created on 17/02/18 by enea dhack.
 */

namespace Enea\Authorization\Exceptions;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;

class NonAssignableGrantableModelException extends AuthorizationException
{
    private $repository;

    private $grantable;

    public function __construct(GrantableOwner $repository, Grantable $grantable)
    {
        $this->repository = get_class($repository);
        $this->grantable = get_class($grantable);
        parent::__construct($this->buildMessage());
    }

    protected function buildMessage()
    {
        return "The {$this->grantable} model can not be assigned to {$this->repository} repository.";
    }
}
