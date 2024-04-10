<?php

use function Pest\Laravel\artisan;

beforeEach(function () {
    $this->moduleTitle = 'Access Control';
    $this->moduleName = 'access-control';
    $this->moduleStudlyName = 'AccessControl';
});
test('can generate a new module', function () {
    // Run modular:make-module, expect a new module to be created
    artisan('modular:make', ['name' => $this->moduleTitle])
        ->expectsQuestion('Module already exists. Do you want to override it?', true)
        ->expectsQuestion('Do you want to activate the new module now?', false);
});

// can activate module
/*test('can activate a module whose directory exists in modules', function () {
    artisan('modular:activate', ['name' => $this->moduleTitle])
        ->expectsOutput('Activating module: '.$this->moduleName)
        ->expectsOutput('./composer.json has been updated')
        ->expectsOutput('Running composer update modular/'.$this->moduleName)
        ->doesntExpectOutputToContain('Your requirements could not be resolved to an installable set of packages')
        ->expectsOutputToContain('Generating optimized autoload files')
        ->assertSuccessful();
});*/

test('should not override existing module unless the user confirms', function () {
    artisan('modular:make', ['name' => $this->moduleTitle])
        ->expectsQuestion('Module already exists. Do you want to override it?', false)
        ->expectsOutput('Failed to create module directories');
});
