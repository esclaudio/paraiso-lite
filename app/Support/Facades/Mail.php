<?php

namespace App\Support\Facades;

class Mail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mailer';
    }
}
