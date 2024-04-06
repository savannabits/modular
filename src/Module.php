<?php

namespace Savannabits\Modular;

use Illuminate\Support\Str;

class Module
{
    private string $name;

    private string $title;

    private string $studlyName;

    private string $namespace;

    private string $basePath;

    /**
     * @throws \Exception
     */
    public function __construct(string $name)
    {
        $this->name = Str::kebab($name);
        $this->title = Str::of($name)->kebab()->title()->replace('-', ' ')->toString();
        // If the module does not exist, throw an error
        if (! is_dir(app()->basePath(config('modular.path').DIRECTORY_SEPARATOR.$this->name))) {
            abort(404, "Module $name does not exist");
        }
        $this->studlyName = Str::of($name)->studly()->toString();
        $this->namespace = config('modular.namespace').'\\'.$this->studlyName;
        $this->basePath = app()->basePath(config('modular.path').DIRECTORY_SEPARATOR.trim($this->name, DIRECTORY_SEPARATOR));
    }

    public function path(string $path = '', bool $relative = false): string
    {
        $basePath = $this->basePath;
        if ($relative) {
            $basePath = config('modular.path').DIRECTORY_SEPARATOR.trim($this->name, DIRECTORY_SEPARATOR);
        }

        return $basePath.($path ? DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR) : '');
    }

    public function makeNamespace(string $relativeNamespace = ''): string
    {
        return $this->namespace.($relativeNamespace ? '\\'.ltrim($relativeNamespace, '\\') : '');
    }

    public function name(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function studlyName(): string
    {
        return $this->studlyName;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function __toString(): string
    {
        return $this->title();
    }

    public function configPath(string $path = '', bool $relative = false): string
    {
        return $this->path('config'.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR), relative: $relative);
    }

    public function databasePath(string $path = '', bool $relative = false): string
    {
        return $this->path('database'.DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR), relative: $relative);
    }

    public function migrationsPath(string $path = '', bool $relative = false): string
    {
        return $this->databasePath('migrations'.DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR), relative: $relative);
    }

    public function seedsPath(string $path = '', bool $relative = false): string
    {
        return $this->databasePath('seeds'.DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR), relative: $relative);
    }

    public function factoriesPath(string $path = '', bool $relative = false): string
    {
        return $this->databasePath('factories'.DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR), $relative);
    }

    public function srcPath(string $path = '', bool $relative = false): string
    {
        return $this->path('src'.DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR), $relative);
    }
}
