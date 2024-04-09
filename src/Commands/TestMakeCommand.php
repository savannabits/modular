<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Support\Str;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class TestMakeCommand extends \Illuminate\Foundation\Console\TestMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-test';

    protected $description = 'Create a new test class in a modular package';

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->getModule()->testsPath(str_replace('\\', '/', $name).'.php');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        if ($this->option('unit')) {
            return $rootNamespace.'\Unit';
        } else {
            return $rootNamespace.'\Feature';
        }
    }
}
