<?php
/**
 * Created on 20/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

trait Assertable
{
    abstract public static function assertTrue($condition, string $message = ''): void;
}
