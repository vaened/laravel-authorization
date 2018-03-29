<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests\Commands;

use Enea\Authorization\Commands\InstallCommand;
use Enea\Authorization\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;
use Mockery;

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

    public function test_command(): void
    {
        $composer = Mockery::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $filesystem = Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('copy')->once()->andReturn(true);
        $command = new InstallCommand($filesystem, $composer);

        $this->app['migration.creator'] = $this->app->make(MigrationCreator::class);

        $this->app->make(Kernel::class)->registerCommand($command);
        $this->artisan('authorization:install');
    }
}

final class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{
    protected function ensureMigrationDoesntAlreadyExist($name)
    {
        //
    }
}