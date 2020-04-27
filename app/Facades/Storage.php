<?php

namespace App\Facades;

class Storage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'storage';
    }
}
