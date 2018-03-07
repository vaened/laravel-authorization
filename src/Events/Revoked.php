<?php
/**
 * Created on 06/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;

class Revoked
{
    private $grantable;

    private $owner;

    public function __construct(GrantableOwner $owner, Grantable $grantable)
    {
        $this->owner = $owner;
        $this->grantable = $grantable;
    }

    public function getGrantable(): Grantable
    {
        return $this->grantable;
    }

    public function getOwner(): GrantableOwner
    {
        return $this->owner;
    }
}
