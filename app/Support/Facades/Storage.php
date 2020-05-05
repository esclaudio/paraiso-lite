<?php

namespace App\Support\Facades;

/**
 * @method static \App\Support\Filesystem\Filesystem disk(string $name)
 *
 * @see \App\Support\Filesystem\FilesystemManager
 */
class Storage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'storage';
    }
}
