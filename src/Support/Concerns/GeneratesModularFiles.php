<?php

namespace Savannabits\Modular\Support\Concerns;

use Illuminate\Support\Str;
use Savannabits\Modular\Facades\Modular;
use Savannabits\Modular\Module;
use Symfony\Component\Console\Input\InputArgument;

trait GeneratesModularFiles
{
    protected function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            ['module', InputArgument::REQUIRED, 'The name of the module in which this should be installed'],
        ]);
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : Modular::packagePath(trim($stub, DIRECTORY_SEPARATOR));
    }

    public function getModule(): Module
    {
        return Modular::module($this->argument('module'));
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return trim($rootNamespace, '\\').'\\'.trim(Str::replace(DIRECTORY_SEPARATOR, '\\', $this->getRelativeNamespace()), '\\');
    }

    protected function getRelativeNamespace(): string
    {
        return '';
    }

    protected function rootNamespace(): string
    {
        return $this->getModule()->getRootNamespace();
    }

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->getModule()->srcPath(str_replace('\\', '/', $name).'.php');
    }
}
