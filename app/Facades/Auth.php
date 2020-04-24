<?php

namespace App\Facades;

class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'auth';
    }
}
