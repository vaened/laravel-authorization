<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Repositories;

use Vaened\Authorization\Contracts\Grantable;
use Vaened\Authorization\Repositories\PermissionRepository;
use Vaened\Authorization\Repositories\Repository;
use Vaened\Authorization\Support\Config;

class PermissionRepositoryTest extends RepositoryTestCase
{
    public function test_can_create_a_permission_from_the_repository(): void
    {
        $this->create('See Articles', 'You can see items from the store');

        $this->assertDatabaseHas($this->table(), [
            'id' => 1,
            'secret_name' => 'see-articles',
            'display_name' => 'See Articles',
            'description' => 'You can see items from the store',
        ]);
    }

    protected function repository(): Repository
    {
        return $this->permissionRepository();
    }

    protected function create(string $name, string $description = null): Grantable
    {
        return $this->permissionRepository()->create($name, $description);
    }

    private function permissionRepository(): PermissionRepository
    {
        return $this->app->make(PermissionRepository::class);
    }

    protected function table(): string
    {
        return Config::permissionTableName();
    }
}
