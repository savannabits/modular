<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Support\Str;
use Savannabits\Modular\Support\Concerns\GeneratesModularFiles;

class ViewMakeCommand extends \Illuminate\Foundation\Console\ViewMakeCommand
{
    use GeneratesModularFiles;

    protected $name = 'modular:make-view';

    protected $description = 'Create a new view file in a modular package';

    protected function getPath($name)
    {
        return $this->viewPath(
            $this->getNameInput().'.'.$this->option('extension'),
        );
    }

    protected function getTestPath(): string
    {
        return $this->getModule()->path(
            Str::of($this->testClassFullyQualifiedName())
                ->replace('\\', '/')
                ->replaceFirst('Tests/Feature', 'tests/Feature')
                ->append('Test.php')
                ->value()
        );
    }

    protected function getTestStub(): string
    {
        $stubName = 'view.'.($this->usingPest() ? 'pest' : 'test').'.stub';
        $stubName = '/stubs/'.$stubName;

        return parent::resolveStubPath($stubName);
    }
}
