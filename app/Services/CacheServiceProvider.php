<?php

namespace App\Services;

use Pimple\ServiceProviderInterface;
use Pimple\Container as Pimple;

class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $container A container instance
     */
    public function register(Pimple $pimple)
    {
        $pimple['cache'] = function ($c) {
            $settings = $c['settings']['redis'];

            $client = new \Predis\Client([
                'scheme' => 'tcp',
                'host' => $settings['host'],
                'port' => $settings['port'],
                'password' => $settings['password'],
            ]);

            return new \App\Cache\RedisAdapter($client);
        };
    }
}