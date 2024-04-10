<?php

use function Pest\Laravel\artisan;

test('can generate a new module', function () {
    // Run modular:make-module, expect a new module to be created
    artisan('modular:make', ['name' => 'Access Control', '--no-interaction' => true])
        ->expectsOutputToContain('Module created successfully.')
        ->assertExitCode(0);
});
