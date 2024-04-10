<?php

namespace Savannabits\Modular;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Modular
{
    public function execCommand(string $command, ?Command $artisan = null): void
    {
        $process = Process::fromShellCommandline($command);
        $process->start();
        foreach ($process as $type => $data) {
            if (! $artisan) {
                echo $data;
            } else {
                $artisan->info(trim($data));
            }
        }
    }

    public function module(string $name): Module
    {
        return new Module($name);
    }

    public function packagePath(string $path = ''): string
    {
        //return the base path of this package
        return dirname(__DIR__.'../').($path ? DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR) : '');
    }
}
