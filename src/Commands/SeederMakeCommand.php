<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Support\Str;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class SeederMakeCommand extends \Illuminate\Database\Console\Seeds\SeederMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-seeder';
    protected $description = 'Create a new seeder class in a modular package';

    protected function rootNamespace(): string
    {
        return $this->getModule()->makeNamespace('Database\\Seeders\\');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace;
    }

    protected function getPath($name): string
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));
        return $this->getModule()->seedersPath($name.'.php');
    }
}
