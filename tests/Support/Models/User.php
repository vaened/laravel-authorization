<?php

declare(strict_types=1);

/**
 * Created by enea dhack - 30/07/17 02:54 PM.
 */

namespace Enea\Authorization\Tests\Support\Models;

use Enea\Authorization\Models\User as Authorizable;

class User extends Authorizable
{
    public $timestamps = false;
}
