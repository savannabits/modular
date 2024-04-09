# Organize your Laravel code into Modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/savannabits/modular.svg?style=flat-square)](https://packagist.org/packages/savannabits/modular)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/savannabits/modular/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/savannabits/modular/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/savannabits/modular/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/savannabits/modular/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/savannabits/modular.svg?style=flat-square)](https://packagist.org/packages/savannabits/modular)

This package offers you the simplest way to organize your Laravel code into modules.
Each module is a separate directory that contains all the necessary files for a complete Laravel package.
We have tried as much as possible to use or extend the existing laravel commands and structures to offer the same rich features that Laravel continues to ship, and make it easier to maintain the package as Laravel evolves.

## Minimum Requirements
- Laravel 11 or higher
- PHP 8.2 or higher

## Installation

You can install the package via composer:

```bash
composer require savannabits/modular
```
Once the package is installed, run the following command to prepare your app for generation of modules:
```bash
php artisan modular:install
```
Follow the prompts to complete the installation.

## Usage

This package offers several commands which allow you to generate standard Laravel files and classes in your modules. 

### Module Generation
However, first you have to generate a module using the following command:
```bash
php artisan modular:make ModuleName
```
The above command will generate a directory structure similar to that of a standard laravel App, with an additional service provider to allow registration of the module.
The module is generated inside the /modules directory of your project. Additionally, the command will proceed to install the module as a symlinked local package in your composer file so that it can be autoloaded.
This process may also be triggered  by using the command
```bash
php artisan modular:activate ModuleName
```

## Activating a module
Run the following command to activate a module:
```bash
php artisan modular:activate ModuleName
```
This command will symlink the module to the composer file and run composer dump-autoload to make the module available to the application.

## Deactivating a module
Run the following command to deactivate a module:
```bash
php artisan modular:deactivate ModuleName
```
This command will remove the module from the composer file and run composer dump-autoload to remove the module from the application.

### Generating a Controller
To generate a controller in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-controller
```

### Generating a Model
To generate a model in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-model
```

### Generating a Migration
To generate a migration in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-migration
```

### Generating a Factory
To generate a factory in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-factory
```

### Generating a Seeder
To generate a seeder in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-seeder
```

### Generating a Policy
To generate a policy in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-policy
```

### Generating a Request
To generate a request in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-request
```

### Generating a Resource
To generate a resource in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-resource
```

### Generating a Test
To generate a test in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-test
```

### Generating a Job
To generate a job in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-job
```

### Generating a Console Command
To generate a console command in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-command
```

### Generating a Provider
To generate a provider in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-provider
```

### Generating a view
To generate a view in a module, run the following command and follow the prompts:
```bash
php artisan modular:make-view
```

## Helpers
The package also offers a few helper functions to make it easier to work with modules.

### Get all modules
To get all modules in the application, you can use the following helper function:
```php
use Savannabits\Modular\Facades\Modular;

$modules = Modular::allModules();
```
The above code will return a collection of instances of the `Savannabits\Modular\Module` class for all active modules in the app.

### Get a module
To get a specific module in the application, you can use the following helper function:
```php
use Savannabits\Modular\Facades\Modular;

$module = Modular::module('ModuleName');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sam Maosa](https://github.com/coolsam726)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
