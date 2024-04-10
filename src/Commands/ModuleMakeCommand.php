<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Savannabits\Modular\Support\Concerns\CanManipulateFiles;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class ModuleMakeCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'modular:make {name?} {--F|force}';

    public $description = 'Create a new module';

    private string $moduleName;

    private string $moduleTitle;

    private string $moduleNamespace;

    private string $modulePath;

    private string $moduleStudlyName;

    public function handle()
    {
        $name = $this->argument('name') ?: text('Enter the module name', 'e.g My Blog MyBlog, my-blog');
        $this->moduleName = Str::of($name)->kebab()->toString();
        $this->moduleStudlyName = Str::of($name)->studly()->toString();
        $this->moduleTitle = Str::of($name)->kebab()->title()->replace('-', ' ')->toString();
        $this->moduleNamespace = config('modular.namespace').'\\'.Str::of($name)->studly()->toString();
        $this->modulePath = config('modular.path').'/'.$this->moduleName;
        $this->info("Creating module: $this->moduleName in $this->modulePath");

        if (! $this->generateModuleDirectories()) {
            $this->error('Failed to create module directories');

            return 1;
        }
        if (! $this->generateModuleFiles()) {
            $this->error('Failed to create module files');

            return 1;
        }

        // Ask if to install the new module
        if (confirm('Do you want to activate the new module now?', false, required: true)) {
            if (! $this->installModule()) {
                $this->error('Failed to activate the new module');

                return 1;
            }
        }

        return 0;
    }

    private function generateModuleDirectories(): bool
    {
        $directories = config('modular.directory_tree');
        if (! count($directories)) {
            $this->error('No directories found in the configuration file');

            return false;
        }
        // If the module exists and force option is not set, confirm that you want to override files
        if (is_dir($this->modulePath) && ! $this->option('force')) {
            if (! confirm('Module already exists. Do you want to override it?')) {
                return false;
            }
        }
        foreach ($directories as $directory) {
            $path = $this->modulePath.'/'.ltrim($directory, '/');
            if (! is_dir($path)) {
                mkdir($path, 0775, true);
                $this->info("Created directory: $path");
            }
        }
        $this->info('Module directories created successfully');

        return true;
    }

    private function generateModuleFiles(): bool
    {
        $this->generateModuleComposerFile();
        $this->generateModuleServiceProvider();
        $this->generatePestFiles();

        return true;
    }

    private function generateModuleComposerFile(): void
    {
        $composerJson = [
            'name' => config('modular.vendor').'/'.$this->moduleName,
            'type' => 'library',
            'description' => $this->moduleTitle.' module',
            'require' => [
                'php' => '^8.2',
            ],
            'autoload' => [
                'psr-4' => [
                    $this->moduleNamespace.'\\' => 'app/',
                    $this->moduleNamespace.'\\Database\\Factories\\' => 'database/factories/',
                    $this->moduleNamespace.'\\Database\\Seeders\\' => 'database/seeders/',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    $this->moduleNamespace.'\\Tests\\' => 'tests/',
                ],
            ],
            'config' => [
                'sort-packages' => true,
                'allow-plugins' => [
                    'pestphp/pest-plugin' => true,
                    'phpstan/extension-installer' => true,
                ],
            ],
            'extra' => [
                'laravel' => [
                    'providers' => [
                        $this->moduleNamespace.'\\'.$this->moduleStudlyName.'ServiceProvider',
                    ],
                ],
            ],
        ];
        $composerJsonPath = $this->modulePath.'/composer.json';
        file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Created composer.json file: $composerJsonPath");
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws FileNotFoundException
     * @throws NotFoundExceptionInterface
     */
    private function generateModuleServiceProvider(): void
    {
        $this->comment('Generating Module Service Provider');
        // get the path to the service provider
        $path = $this->modulePath.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$this->moduleStudlyName.'ServiceProvider.php';
        $namespace = $this->moduleNamespace;
        $class = $this->moduleStudlyName.'ServiceProvider';
        $this->copyStubToApp('module.provider', $path, [
            'class' => $class,
            'namespace' => $namespace,
            'name' => $this->moduleName,
        ]);
    }

    private function generatePestFiles(): void
    {
        // phpunit.xml
        $path = $this->modulePath.DIRECTORY_SEPARATOR.'phpunit.xml';
        $this->copyStubToApp('phpunit', $path, [
            'moduleName' => $this->moduleStudlyName,
        ]);

        // Pest.php
        $path = $this->modulePath.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'Pest.php';
        $this->copyStubToApp('pest.class', $path, [
            'namespace' => $this->moduleNamespace,
        ]);

        // TestCase.php
        $path = $this->modulePath.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'TestCase.php';
        $this->copyStubToApp('test-case', $path, [
            'namespace' => $this->moduleNamespace.'\\Tests',
        ]);
    }

  
    private function installModule(): bool
    {
        $this->comment('Activating the new Module');
        $this->call('modular:activate', ['name' => $this->moduleName]);

        return true;
    }
}
