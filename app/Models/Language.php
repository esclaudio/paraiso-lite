<?php

namespace App\Models;

abstract class Language
{
    const EN    = 'en';
    const ES_AR = 'es_AR';

    public static function all()
    {
        return [
            self::EN    => 'English',
            self::ES_AR => 'Spanish (Argentina)',
        ];
    }
}
