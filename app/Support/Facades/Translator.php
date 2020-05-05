<?php

namespace App\Support\Facades;

class Translator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translator';
    }
}
