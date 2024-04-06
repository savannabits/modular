<?php

namespace Savannabits\Modular\Commands;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Savannabits\Modular\Facades\Modular;
use Symfony\Component\Console\Input\InputArgument;

class MigrationMakeCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'modular:make-migration
        {name : The name of the migration}
        {module : The module to create the migration for}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        ';
    protected $description = 'Create a new migration file in a module';

    public function handle(): void
    {
        $this->call('make:migration', [
            'name' => $this->argument('name'),
            '--create' => $this->option('create'),
            '--table' => $this->option('table'),
            '--path' => $this->getMigrationPath(),
        ]);
    }

    protected function getMigrationPath(): string
    {
        return  Modular::module($this->argument('module'))->migrationsPath(relative: true);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => ['What should the migration name be?','e.g create_planets_table'],
            'module' => ['Enter the module name (It has to be existing)','e.g Blog, BlogPost, blog-post']
        ];
    }
}
