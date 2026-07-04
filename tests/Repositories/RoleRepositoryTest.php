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
use Vaened\Authorization\Repositories\Repository;
use Vaened\Authorization\Repositories\RoleRepository;
use Vaened\Authorization\Support\Config;

class RoleRepositoryTest extends RepositoryTestCase
{
    public function test_can_create_a_role_from_the_repository(): void
    {
        $this->create('Articles Owner', 'Manage the articles in their entirety');

        $this->assertDatabaseHas($this->table(), [
            'id' => 1,
            'secret_name' => 'articles-owner',
            'display_name' => 'Articles Owner',
            'description' => 'Manage the articles in their entirety',
        ]);
    }

    protected function repository(): Repository
    {
        return $this->roleRepository();
    }

    protected function create(string $name, string $description = null): Grantable
    {
        return $this->roleRepository()->create($name, $description);
    }

    private function roleRepository(): RoleRepository
    {
        return $this->app->make(RoleRepository::class);
    }

    protected function table(): string
    {
        return Config::roleTableName();
    }
}
