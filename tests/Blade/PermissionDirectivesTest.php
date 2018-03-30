<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Blade;

class PermissionDirectivesTest extends DirectiveTestCase
{
    public function test_the_authenticated_can_directive_correctly_evaluates_the_owner_permission(): void
    {
        $user = $this->getLoggedUser();
        $permission = $this->permission('Edit Articles');
        $user->grant($permission);
        $this->assertSame('has permission', $this->compile('can', ['permission' => 'edit-articles']));
        $user->revoke($permission);
        $this->assertSame('does not have permission', $this->compile('can', ['permission' => 'edit-articles']));
    }

    public function test_the_authenticated_cannot_directive_correctly_evaluates_the_owner_permission(): void
    {
        $user = $this->getLoggedUser();
        $this->assertSame('does not have permission', $this->compile('cannot', ['permission' => 'edit-articles']));
        $user->grant($this->permission('Edit Articles'));
        $this->assertSame('has permission', $this->compile('cannot', ['permission' => 'edit-articles']));
    }
}
