<?php

namespace Savannabits\Modular\Commands;

use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class JobMakeCommand extends \Illuminate\Foundation\Console\JobMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-job';

    protected $description = 'Create a new job class in a modular package';

    protected function getRelativeNamespace(): string
    {
        return 'Jobs';
    }
}
