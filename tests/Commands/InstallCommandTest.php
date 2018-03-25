<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Commands;

use Enea\Authorization\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class InstallCommandTest extends TestCase
{
    public function test_the_command_was_added_to_artisan(): void
    {
        $this->assertArrayHasKey('authorization:install', Artisan::all());
    }

    public function test_the_configuration_file_is_published(): void
    {
        $this->artisan('authorization:install');
        $this->assertFileExists(base_path('config/authorization.php'));
    }
}
