<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Savannabits\Modular\Facades\Modular;

use function Laravel\Prompts\text;

class DeactivateModuleCommand extends Command
{
    public $signature = 'modular:deactivate {name?}';

    public $description = 'Deactivate a module';

    private string $moduleName;

    public function handle(): void
    {
        $this->moduleName = Str::kebab($this->argument('name') ?? text('Enter the module name', 'e.g My Blog MyBlog, my-blog'));
        $this->info("Activating module: $this->moduleName");
        $this->deactivateModule();
    }

    private function deactivateModule(): void
    {
        $moduleName = $this->moduleName;
        $repoName = config('modular.vendor', 'modular').'/'.$moduleName;
        Modular::execCommand("composer remove $repoName");
    }
}
