<?php

declare(strict_types=1);

namespace Vaened\Authorization\Console;

use Illuminate\Console\Command;
use Vaened\Authorization\Configuration\Synchronization;

use function glob;
use function is_array;
use function is_file;
use function Laravel\Prompts\multiselect;

final class InstallAuthorization extends Command
{
    protected $signature   = 'authorization:install';

    protected $description = 'Publish Laravel Authorization resources';

    public function handle(): int
    {
        $this->components->info('Laravel Authorization installation');
        $this->newLine();

        $resources = $this->resources();

        $options = array_map(static fn($resource) => $resource['name'], $resources);

        $selected = $this->input->isInteractive()
            ? multiselect(
                label  : 'Which resources do you want to publish?',
                options: $options,
                default: array_keys($options),
                info   : fn(string $key): string => $resources[$key]['description'],
            )
            : array_keys($options);

        foreach ($selected as $key) {
            $resource = $resources[$key];

            $this->publishResource(
                $resource['name'],
                $resource['tag'],
                $resource['exists'],
            );
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, array{name: string, description: string, tag: string, exists: bool}>
     */
    private function resources(): array
    {
        return [
            'config'      => [
                'name'        => 'Package configuration',
                'description' => 'Package settings for tables, cache, middleware, and Gate.',
                'tag'         => 'laravel-authorization-config',
                'exists'      => is_file(config_path('authorization.php')),
            ],
            'definitions' => [
                'name'        => 'Authorization definitions',
                'description' => 'Roles and permissions used by authorization:sync.',
                'tag'         => 'laravel-authorization-definitions',
                'exists'      => is_file(config_path(Synchronization::filename() . '.php')),
            ],
            'migrations'  => [
                'name'        => 'Database migrations',
                'description' => 'Database tables for roles, permissions, and assignments.',
                'tag'         => 'laravel-authorization-migrations',
                'exists'      => $this->hasAuthorizationMigration(),
            ],
        ];
    }

    private function publishResource(string $name, string $tag, bool $exists): void
    {
        if ($exists) {
            $this->components->warn("Already exists; skipped: $name.");

            return;
        }

        $this->callSilently('vendor:publish', ['--tag' => $tag]);
        $this->components->info("Published: $name.");
    }

    private function hasAuthorizationMigration(): bool
    {
        $files = glob(database_path('migrations/*_create_laravel_authorization_tables.php'));

        return is_array($files) && $files !== [];
    }
}
