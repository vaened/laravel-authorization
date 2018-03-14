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
        $this->setTable(config("authorization.tables.{$this->getConfigTableKeyName()}"));
        parent::__construct($attributes);
    }

    abstract protected function getConfigTableKeyName(): string;

    public function __toString()
    {
        return $this->getAttribute('secret_name');
    }
}
