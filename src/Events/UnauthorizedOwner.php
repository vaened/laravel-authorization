<?php
declare(strict_types=1);

/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Events;

use Illuminate\Database\Eloquent\Model;

class UnauthorizedOwner
{
    private $authorizable;

    private $grantables;

    public function __construct(Model $authorizable, array $grantables)
    {
        $this->authorizable = $authorizable;
        $this->grantables = $grantables;
    }

    public function getAuthorizable(): Model
    {
        return $this->authorizable;
    }

    public function getGrantables(): array
    {
        return $this->grantables;
    }
}
