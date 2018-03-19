<?php
/**
 * Created on 06/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\GrantableOwner;
use Illuminate\Support\Collection;

class Revoked implements Operation
{
    private $grantableCollection;

    private $owner;

    public function __construct(GrantableOwner $owner, Collection $grantableCollection)
    {
        $this->owner = $owner;
        $this->grantableCollection = $grantableCollection;
    }

    public function getGrantableCollection(): Collection
    {
        return $this->grantableCollection;
    }

    public function getOwner(): GrantableOwner
    {
        return $this->owner;
    }
}
