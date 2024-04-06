<?php

namespace Savannabits\Modular\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Savannabits\Modular\Facades\Modular;

use function Laravel\Prompts\text;

class ActivateModuleCommand extends Command
{
    public $signature = 'modular:activate {name?}';

    public $description = 'Activate a module';

    private string $moduleName;

    public function handle(): void
    {
        $this->moduleName = Str::kebab($this->argument('name') ?? text('Enter the module name', 'e.g My Blog MyBlog, my-blog'));
        $this->info("Activating module: $this->moduleName");
        $this->activateModule();
    }

    private function activateModule(): void
    {
        $moduleName = $this->moduleName;
        $repoName = config('modular.vendor', 'modular').'/'.$moduleName;
        Modular::execCommand('composer require '.$repoName.':@dev');
        Modular::execCommand("php artisan $moduleName:install");
    }
}
