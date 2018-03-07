<?php
/**
 * Created on 06/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\{
    Grantable, GrantableOwner
};

class Granted
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
