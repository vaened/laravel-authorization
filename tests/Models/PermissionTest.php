<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Tests\Models;

use Vaened\Authorization\Contracts\Grantable;

class PermissionTest extends AuthorizationTestCase
{
    protected function authorization(string $name): Grantable
    {
        return $this->permission($name);
    }
}
