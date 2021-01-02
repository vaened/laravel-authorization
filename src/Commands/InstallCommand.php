<?php

declare(strict_types=1);

/**
 * Created on 22/03/18 by enea dhack.
 */

namespace Enea\Authorization\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class InstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'authorization:install';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Install the migration and configuration files for laravel-authorization';

    private $files;

    private $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle(): void
    {
        $this->publishConfig();
        $this->publishMigration();

        $this->info('Successfully installed laravel-authorization!');
        $this->composer->dumpAutoloads();
    }

    private function publishConfig(): void
    {
        $this->comment('Publishing the config file...');
        $this->callSilent('vendor:publish', ['--tag' => 'config']);
    }

    private function publishMigration(): void
    {
        $this->comment('Publishing the migration file...');
        $this->callSilent('vendor:publish', ['--tag' => 'migrations']);
    }
}
