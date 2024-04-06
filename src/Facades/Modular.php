<?php

namespace Savannabits\Modular\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Savannabits\Modular\Modular
 */
class Modular extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Savannabits\Modular\Modular::class;
    }
}
