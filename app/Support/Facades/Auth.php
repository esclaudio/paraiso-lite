<?php

namespace App\Support\Facades;

class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'auth';
    }
}
