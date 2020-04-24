<?php

namespace App\Facades;

use Pimple\Container;

class Facade
{
    /**
     * When you writing the facades extended Facade, you can use "self::$app"
     * to get anything you want.
     * @var Pimple\Container $app slim app instance.
     */
    public static $container;

    /**
     * @param Slim\App $app [description]
     */
    public static function setFacadeContainer(Container $container)
    {
        Facade::$container = $container;
    }

    public static function __callStatic($method, $args)
    {
        return static::self()->$method(...$args);
    }

    /**
     * Set the service name to static proxy.
     * You can override this function to set a facade for the service name you
     * returned.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return '';
    }

    /**
     * Set the instance which needs facades.
     * You can override this function to set a facade for the instance you
     * returned.
     * @return mixed
     */
    public static function self()
    {
        return Facade::$container[static::getFacadeAccessor()];
    }
}
