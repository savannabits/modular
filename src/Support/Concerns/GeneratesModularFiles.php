<?php

namespace Savannabits\Modular\Support\Concerns;

use Illuminate\Support\Str;
use Savannabits\Modular\Facades\Modular;
use Savannabits\Modular\Module;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

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
            : (file_exists($packagePath = Modular::packagePath(trim($stub, DIRECTORY_SEPARATOR))) ? $packagePath : Modular::packagePath('src/Commands/'.trim($stub, DIRECTORY_SEPARATOR)));
    }

    public function getModule(): Module
    {
        try {
            return Modular::module($this->argument('module'));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            exit(1);
        }
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

    protected function possibleModels()
    {
        $modelPath = $this->getModule()->srcPath('Models');

        return collect(Finder::create()->files()->depth(0)->in($modelPath))
            ->map(fn ($file) => $file->getBasename('.php'))
            ->sort()
            ->values()
            ->all();
    }

    protected function viewPath($path = ''): string
    {
        $views = $this->getModule()->resourcePath('views');

        return $views.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
