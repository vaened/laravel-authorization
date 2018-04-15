<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Events;

use Enea\Authorization\Contracts\Owner;
use Illuminate\Support\Collection;

class Denied implements Operation
{
    private $grantableCollection;

    private $owner;

    public function __construct(Owner $owner, Collection $grantableCollection)
    {
        $this->owner = $owner;
        $this->grantableCollection = $grantableCollection;
    }

    public function getGrantableCollection(): Collection
    {
        return $this->grantableCollection;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }
}