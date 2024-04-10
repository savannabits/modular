<?php

namespace Savannabits\Modular\Commands;

use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class ResourceMakeCommand extends \Illuminate\Foundation\Console\ResourceMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-resource';

    protected $description = 'Create a new resource class in a modular package';

    protected function getRelativeNamespace(): string
    {
        return '\\Http\\Resources';
    }
}
