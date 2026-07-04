<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

abstract class Grantable extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->setTable($this->getConfigTableName());
        parent::__construct($attributes);
    }

    abstract protected function getConfigTableName(): string;

    public function getFillable()
    {
        return [
            'secret_name',
            'display_name',
            'description'
        ];
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return Carbon::instance($date)->toDateTimeString();
    }

    public function __toString()
    {
        return $this->getAttribute('secret_name');
    }
}
