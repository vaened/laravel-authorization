<?php
/**
 * Created on 22/03/18 by enea dhack.
 */

namespace Enea\Authorization\Commands;

use Enea\Authorization\AuthorizationServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;

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
        $this->info('Publishing the config file');

        Artisan::call('vendor:publish', [
            '--provider' => AuthorizationServiceProvider::class,
        ]);
    }

    private function publishMigration(): void
    {
        $this->info('Publishing the migration file');
        $source = __DIR__ . '/../../database/migrations/create_laravel_authorization_tables.stub';
        $destination = $this->laravel->make('migration.creator')->create('create_laravel_authorization_tables', database_path('migration'));

        $this->files->copy($source, $destination);
    }
}
