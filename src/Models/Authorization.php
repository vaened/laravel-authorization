<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Vaened\Sentinel\Authorization as AuthorizationContract;

abstract class Authorization extends Model implements AuthorizationContract
{
    public $timestamps = false;

    protected $guarded = [];

    abstract protected function tableName(): string;

    public function id(): int|string
    {
        return $this->getKey();
    }

    public function code(): string
    {
        return $this->getAttributeValue('code');
    }

    public function name(): string
    {
        return $this->getAttributeValue('name');
    }

    public function description(): string|null
    {
        return $this->getAttributeValue('description');
    }

    public function getTable(): string
    {
        return $this->tableName();
    }
}
