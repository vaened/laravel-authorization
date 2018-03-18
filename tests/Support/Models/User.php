<?php
/**
 * Created by enea dhack - 30/07/17 02:54 PM.
 */

namespace Enea\Authorization\Tests\Support\Models;

use Enea\Authorization\Contracts\Authorizable as AuthorizedUserContract;
use Enea\Authorization\Traits\Authorizable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthorizedUserContract, AuthenticatableContract
{
    use Authorizable, Authenticatable;

    public $timestamps = false;
}
