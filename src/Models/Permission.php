<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 *
 * @package Enea\Authorization\Models
 * @author enea dhack <enea.so@live.com>
 *
 * @property int id
 * @property string secret_name
 */
class Permission extends Model implements PermissionContract
{
    /**
     * {@inheritdoc}
     */
    public function getSecretName(): string
    {
        return $this->secret_name;
    }
}
