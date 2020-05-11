<?php

namespace App\Services;

use Predis\Client;
use Pimple\ServiceProviderInterface;
use Pimple\Container as Pimple;
use App\Cache\RedisAdapter;
use App\Cache\ArrayAdapter;

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
        $driver = $pimple['settings']['cache_driver'];

        $pimple['cache'] = function ($c) use ($driver) {
            if ($driver === 'redis') {
                $settings = $c['settings']['redis'];
    
                $client = new Client([
                    'scheme' => 'tcp',
                    'host' => $settings['host'],
                    'port' => $settings['port'],
                    'password' => $settings['password'],
                ]);
    
                return new RedisAdapter($client);
            }

            return new ArrayAdapter();
        };
    }
}