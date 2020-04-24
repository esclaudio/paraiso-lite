<?php

namespace App\Facades;

class Request extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'request';
    }
}
