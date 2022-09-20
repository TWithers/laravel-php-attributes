<?php

namespace TWithers\LaravelAttributes\Facades;

use Illuminate\Support\Facades\Facade;

class Attributes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'attributes';
    }
}