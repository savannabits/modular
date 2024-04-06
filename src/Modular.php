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
}
