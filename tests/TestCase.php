<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vaened\Authorization\Models\Permission;
use Vaened\Authorization\Models\Role;
use Vaened\Authorization\Models\Subject;

abstract class TestCase extends OrchestraTestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('authorization.tables.roles', 'roles');
        $app['config']->set('authorization.tables.permissions', 'permissions');
        $app['config']->set('authorization.tables.role_permissions', 'role_permissions');
        $app['config']->set('authorization.tables.subject_roles', 'subject_roles');
        $app['config']->set('authorization.tables.subject_permissions', 'subject_permissions');
    }

    protected function subject(array $attributes = []): Subject
    {
        return Subject::query()->create($attributes);
    }

    protected function role(
        string $code,
        string $name,
        string|null $description = null,
    ): Role {
        return Role::query()->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
        ]);
    }

    protected function permission(
        string $code,
        string $name,
        string|null $description = null,
    ): Permission {
        return Permission::query()->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
        ]);
    }
}
