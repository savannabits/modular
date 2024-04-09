<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Savannabits\Modular\Facades\Modular;
use Savannabits\Modular\Support\Concerns\CanManipulateFiles;

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

        $this->generateModuleDirectories();
        $this->generateModuleFiles();
        $this->installModule();
    }

    private function generateModuleDirectories(): bool
    {
        $directories = config('modular.directory_tree');
        if (! count($directories)) {
            $this->error('No directories found in the configuration file');

            return false;
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

    private function generateModuleFiles(): void
    {
        $this->generateModuleComposerFile();
        try {
            $this->generateModuleServiceProvider();
        } catch (FileNotFoundException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this->error($e->getMessage());
        }
        $this->generatePestFiles();
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
        $path = Modular::module($this->moduleName)->appPath($this->moduleStudlyName.'ServiceProvider.php');
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
        $path = Modular::module($this->moduleName)->path('phpunit.xml');
        $this->copyStubToApp('phpunit', $path, [
            'moduleName' => $this->moduleStudlyName,
        ]);

        // Pest.php
        $path = Modular::module($this->moduleName)->testsPath('Pest.php');
        $this->copyStubToApp('pest.class', $path, [
            'namespace' => $this->moduleNamespace,
        ]);

        // TestCase.php
        $path = Modular::module($this->moduleName)->testsPath('TestCase.php');
        $this->copyStubToApp('test-case', $path, [
            'namespace' => $this->moduleNamespace.'\\Tests',
        ]);
    }

    private function installModule(): void
    {
        $this->comment('Activating the new Module');
        $this->call('modular:activate', ['name' => $this->moduleName]);
    }
}
