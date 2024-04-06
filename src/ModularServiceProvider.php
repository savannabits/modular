<?php

namespace Savannabits\Modular;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ModularServiceProvider extends PackageServiceProvider
{
    public static string $name = 'modular';

    public static string $vendor = 'savannabits';

    public static string $viewNamespace = 'modular';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name(static::$name)
            ->hasConfigFile('modular')
            ->hasViews(static::$viewNamespace)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                // get repo name from composer.json
                $name = json_decode(file_get_contents(base_path('composer.json')))?->name;
                $command
                    ->askToStarRepoOnGitHub($name)
                    ->startWith(fn (InstallCommand $command) => $this->installationSteps($command));
            });
        $this->mergeConfigFrom($this->package->basePath('/../config/modular.php'), 'modular');
    }

    private function configureComposerMerge(InstallCommand $command): void
    {
        $command->comment('Configuring Composer merge plugin:');
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
        // Add the modules repositories into compose if they don't exist
        if (! isset($composerJson['repositories'])) {
            $composerJson['repositories'] = [];
        }
        if (! collect($composerJson['repositories'])->contains(fn ($repo) => $repo['type'] === 'path' && $repo['url'] === config('modular.path').'/*')) {
            $composerJson['repositories'][] = [
                'type' => 'path',
                'url' => config('modular.path').'/*',
                'options' => [
                    'symlink' => true,
                ],
            ];
        }
        if (! isset($composerJson['extra']['merge-plugin'])) {
            $composerJson['extra']['merge-plugin'] = [
                'include' => [
                    'modules/*/composer.json',
                ],
                'replace' => true,
                'merge-extra' => true,
                'merge-extra-deep' => true,
                'merge-scripts' => true,

            ];

            // Ensure the composer-merge-plugin is in the list of allowed plugins
            if (! isset($composerJson['config']['allow-plugins'])) {
                $composerJson['config']['allow-plugins'] = [];
            }
            // If allowed-plugins is set to true, disregard
            if ($composerJson['config']['allow-plugins'] === true) {
                $command->warn('Composer merge plugin already configured. skipping...');
            } else {
                $composerJson['config']['allow-plugins']['wikimedia/composer-merge-plugin'] = true;
            }
        }
        file_put_contents(base_path('composer.json'), json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $command->info('Composer file configured successfully');
    }

    private function installationSteps(InstallCommand $command): void
    {
        $this->ensureModularPathExists($command);
        $this->configureComposerMerge($command);
        // Run composer dump-autoload and pipe the output realtime
        $command->comment('Running composer dump-autoload:');
        \Savannabits\Modular\Facades\Modular::execCommand('composer dump-autoload');
        $command->info('Composer dump-autoload completed successfully');
    }

    private function ensureModularPathExists(InstallCommand $command): void
    {
        $command->comment('Ensuring modular path exists:');
        $path = config('modular.path');
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
            $command->info("Directory $path created successfully");
        } else {
            $command->warn("Directory $path already exists. skipping...");
        }
    }

    private function getMigrationFiles(): array
    {
        return array_merge($this->discoverMigrations(), [
            // Your other migrations
        ]);
    }

    private function getCommands(): array
    {
        return array_merge($this->discoverCommands(), [
            // Your other commands
        ]);
    }

    private function discoverMigrations(): array
    {
        // Get an array of file names from the migrations directory
        $glob1 = glob($this->package->basePath('/../database/migrations/*.php'));
        $glob2 = glob($this->package->basePath('/../database/migrations/*.php.stub'));

        return collect($glob1)
            ->merge($glob2)
            ->map(fn ($filename) => Str::of($filename)
                ->afterLast('/')
                ->rtrim('.php.stub')
                ->rtrim('.php')->toString()
            )
            ->toArray();
    }

    private function discoverCommands(): array
    {
        // automatically include all namespace classes in the Console directory
        // use glob to return full paths to all files in the Console directory

        $paths = glob($this->package->basePath('/Commands/*.php'));

        return collect($paths)
            ->map(fn ($filename) => $this->getNamespaceFromFile($filename)->toString())
            ->toArray();
    }

    private function getNamespaceFromFile($path): Stringable
    {
        return $this->getFullNamespace(Str::of($path)->afterLast('src/')
            ->replace('/', '\\')
            ->studly()->rtrim('.php'));
    }

    private function getFullNamespace(string $relativeNamespace = ''): Stringable
    {
        return Str::of(static::$vendor)->studly()
            ->append('\\')
            ->append(Str::studly(static::$name))
            ->append('\\')
            ->append(Str::studly($relativeNamespace));
    }
}
