<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Blade;

class RoleDirectivesTest extends DirectiveTestCase
{
    public function test_the_authenticated_is_directive_correctly_evaluates_the_owner_role(): void
    {
        $user = $this->getLoggedUser();
        $role = $this->role('Articles Creator');
        $user->grant($role);
        $this->assertSame('is member', $this->compile('is', ['role' => 'articles-creator']));
        $user->revoke($role);
        $this->assertSame('non member', $this->compile('is', ['role' => 'articles-creator']));
    }

    public function test_the_authenticated_isnt_directive_correctly_evaluates_the_owner_role(): void
    {
        $user = $this->getLoggedUser();
        $this->assertSame('non member', $this->compile('isnt', ['role' => 'articles-creator']));
        $user->grant($this->role('Articles Creator'));
        $this->assertSame('is member', $this->compile('is', ['role' => 'articles-creator']));
    }
}
