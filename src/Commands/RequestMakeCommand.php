<?php

namespace Savannabits\Modular\Commands;

use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class RequestMakeCommand extends \Illuminate\Foundation\Console\RequestMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-request';

    protected $description = 'Create a new form request class in a modular package';

    protected function getRelativeNamespace(): string
    {
        return '\\Http\\Requests';
    }
}
