<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Grantable extends Model
{
    protected $configTableKeyName;

    public function __construct(array $attributes = [])
    {
        $this->setTable(config("authorization.tables.{$this->configTableKeyName}"));
        parent::__construct($attributes);
    }
}
