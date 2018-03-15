<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Grantable extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->setTable($this->getConfigTableName());
        parent::__construct($attributes);
    }

    abstract protected function getConfigTableName(): string;

    public function __toString()
    {
        return $this->getAttribute('secret_name');
    }
}
