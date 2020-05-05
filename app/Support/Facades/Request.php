<?php

namespace App\Support\Facades;

class Request extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'request';
    }
}
