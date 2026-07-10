<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration;

use Vaened\Authorization\Errors\UnsupportedSubject;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Authorization\Tests\Runtime\TestSubject;
use Vaened\Sentinel\Cache\AuthorizationCacheStore;
use Vaened\Sentinel\Repositories\SubjectRoleRepository;
use Vaened\Sentinel\SubjectPermissionState;

final class AuthorizerFlowTest extends DatabaseTestCase
{
    public function test_it_integrates_the_full_laravel_flow_and_keeps_the_subject_projection_in_sync(): void
    {
        $cache      = $this->app->make(AuthorizationCacheStore::class);
        $subject    = $this->subject();
        $role       = $this->role('admin', 'Administrator');
        $permission = $this->permission('users.read', 'Read Users');

        self::assertNull($cache->get($subject));

        $role->grant($permission);
        $subject->grant($role);

        self::assertTrue($subject->actsAs('admin'));
        self::assertTrue($subject->can('users.read'));

        self::assertDatabaseHas('subject_roles', [
            'role_id'           => $role->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);

        $projection = $cache->get($subject);
        self::assertNotNull($projection);
        self::assertSame(['admin'], $projection->roles()->codes());
        self::assertSame(SubjectPermissionState::Inherited, $projection->permissions()->find('users.read')?->state());

        $subject->deny($permission);

        self::assertFalse($subject->can('users.read'));
        self::assertDatabaseHas('subject_permissions', [
            'permission_id'     => $permission->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
            'denied'            => true,
        ]);

        $projection = $cache->get($subject);
        self::assertNotNull($projection);
        self::assertSame(['admin'], $projection->roles()->codes());
        self::assertSame(SubjectPermissionState::Denied, $projection->permissions()->find('users.read')?->state());

        $subject->revoke($permission);

        self::assertNull(
            $cache->get($subject),
            'Revoking a direct subject permission should evict the cached projection so it can be rebuilt.',
        );

        self::assertTrue($subject->can('users.read'));

        $projection = $cache->get($subject);
        self::assertNotNull($projection);
        self::assertSame(['admin'], $projection->roles()->codes());
        self::assertSame(SubjectPermissionState::Inherited, $projection->permissions()->find('users.read')?->state());

        $subject->revoke($role);

        self::assertNull(
            $cache->get($subject),
            'Revoking a role from the subject should evict the cached projection for that subject.',
        );

        self::assertFalse($subject->actsAs('admin'));
        self::assertFalse($subject->can('users.read'));

        self::assertDatabaseMissing('subject_roles', [
            'role_id'           => $role->id(),
            'authorizable_type' => $subject->getMorphClass(),
            'authorizable_id'   => $subject->id(),
        ]);

        $projection = $cache->get($subject);
        self::assertNotNull($projection);
        self::assertSame([], $projection->roles()->codes());
        self::assertSame([], $projection->permissions()->codes());
    }

    public function test_it_rejects_subjects_that_do_not_extend_eloquent_models(): void
    {
        $repository = $this->app->make(SubjectRoleRepository::class);

        $this->expectException(UnsupportedSubject::class);
        $this->expectExceptionMessage(TestSubject::class);

        $repository->allOf(new TestSubject(1));
    }
}
