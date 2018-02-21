<?php
/**
 * Created on 14/02/18 by enea dhack.
 */

namespace Enea\Authorization\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

interface Serializable extends Arrayable, Jsonable, JsonSerializable
{
}
