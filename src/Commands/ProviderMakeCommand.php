<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\ServiceProvider;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'modular:make-provider')]
class ProviderMakeCommand extends GeneratorCommand
{
    use GeneratesModularFiles;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'modular:make-provider';

    protected $description = 'Create a new Service provider class in a modular package';

    protected $type = 'Provider';

    public function handle(): ?bool
    {
        $result = parent::handle();

        if ($result === false) {
            return $result;
        }

        return $result;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/provider.stub');
    }

    protected function getRelativeNamespace(): string
    {
        return '\\Providers';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the provider already exists'],
        ];
    }
}

