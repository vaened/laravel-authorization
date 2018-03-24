<?php
/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Enea\Authorization\Contracts\Grantable;

class PermissionTest extends AuthorizationTestCase
{
    protected function authorization(string $name): Grantable
    {
        return $this->permission(['display_name' => $name]);
    }
}
