<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Support\Models;

use Vaened\Authorization\Models\User as Authorizable;

class User extends Authorizable
{
    public $timestamps = false;
}
