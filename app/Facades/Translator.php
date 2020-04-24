<?php

namespace App\Facades;

class Translator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translator';
    }
}
