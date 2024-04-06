<?php

namespace Savannabits\Modular;

class Modular
{
    public function execCommand(string $command): void
    {
        $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, base_path());
        if (is_resource($process)) {
            proc_close($process);
        }
    }

    public function module(string $name): Module
    {
        return new Module($name);
    }

    public function packagePath(string $path = ''): string
    {
        //return the base path of this package
        return __DIR__ . '/../'.($path ? DIRECTORY_SEPARATOR . trim($path,DIRECTORY_SEPARATOR) : '');
    }
}
