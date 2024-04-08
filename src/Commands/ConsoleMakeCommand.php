<?php

namespace Savannabits\Modular\Commands;


use Savannabits\Modular\Facades\Modular;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class ConsoleMakeCommand extends \Illuminate\Foundation\Console\ConsoleMakeCommand
{
    use GeneratesModularFiles;
    protected $name = 'modular:make-command';

    protected $description = 'Create a new Artisan command in a modular package';

    protected function getStub(): string
    {
        $relativePath = '/stubs/console.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : (file_exists($packagePath = Modular::packagePath(trim($relativePath, '/'))) ? $packagePath : __DIR__.$relativePath);
    }

    protected function getRelativeNamespace(): string
    {
        return '\\Console\\Commands';
    }
}
