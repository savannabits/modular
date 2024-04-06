<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as BaseModelMakeCommand;
use Illuminate\Support\Str;
use Savannabits\Modular\Facades\Modular;
use Savannabits\Modular\Module;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'modular:make-model')]
class ModelMakeCommand extends BaseModelMakeCommand
{
    use GeneratesModularFiles;
    protected $name = 'modular:make-model';

    protected $description = 'Create a new Eloquent model class in a modular package';
    protected function createFactory(): void
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('modular:make-factory', [
            'name' => "{$factory}Factory",
            'module' => $this->getModule()->name(),
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    /**
     * Create a migration file for the model.
     */
    protected function createMigration(): void
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('modular:make-migration', [
            'name' => "create_{$table}_table",
            'module' => $this->getModule()->name(),
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the model.
     */
    protected function createSeeder(): void
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('make:seeder', [
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Create a controller for the model.
     */
    protected function createController(): void
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call('make:controller', array_filter([
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') || $this->option('api') ? $modelName : null,
            '--api' => $this->option('api'),
            '--requests' => $this->option('requests') || $this->option('all'),
            '--test' => $this->option('test'),
            '--pest' => $this->option('pest'),
        ]));
    }

    /**
     * Create a policy file for the model.
     */
    protected function createPolicy(): void
    {
        $policy = Str::studly(class_basename($this->argument('name')));

        $this->call('make:policy', [
            'name' => "{$policy}Policy",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    protected function getRelativeNamespace(): string
    {
        return 'Models';
    }
}
