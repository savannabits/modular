<?php

namespace Savannabits\Modular\Commands;

use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class PolicyMakeCommand extends \Illuminate\Foundation\Console\PolicyMakeCommand
{
    use GeneratesModularFiles;
    protected $name = 'modular:make-policy';
    protected $description = 'Create a new policy class in a modular package';

    protected function getRelativeNamespace(): string
    {
        return '\\Policies';
    }
}
