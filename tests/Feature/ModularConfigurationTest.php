<?php

use function Pest\Laravel\artisan;

test('modular installs successfully', function () {
    // Run modular:install, expect composer to have an entry for a local modules/* repository
    artisan('modular:install', ['--no-interaction' => true])
        ->expectsQuestion('Would you like to star our repo on GitHub?', 'no')
        ->expectsOutputToContain('modular has been installed!');
});

test('composer has been configured with modules/* repository', function () {
    // Run artisan modular:install command
    $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
    expect($composerJson['repositories'])->toBeArray()
        ->and($composerJson['repositories'])->toContain([
            'type' => 'path',
            'url' => 'modules/*',
            'options' => [
                'symlink' => true,
            ],
        ]);
});
