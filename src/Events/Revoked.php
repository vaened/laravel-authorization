<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Events;

use Vaened\Authorization\Contracts\Owner;
use Illuminate\Support\Collection;

class Revoked implements Operation
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
