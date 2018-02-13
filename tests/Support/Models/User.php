<?php
/**
 * Created by enea dhack - 30/07/17 02:54 PM.
 */

namespace Enea\Authorization\Test\Support\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $id
 */
class User extends Model
{
    public $timestamps = false;
}
