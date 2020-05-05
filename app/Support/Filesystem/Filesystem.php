<?php

namespace App\Support\Filesystem;

use RuntimeException;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Config;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Adapter\AbstractAdapter;

class Filesystem
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $driver;

    public function __construct(AbstractAdapter $adapter)
    {
        $this->driver = new LeagueFilesystem($adapter);
    }

    public function exists(string $path): bool
    {
        return $this->driver->has($path);
    }

    public function path($path)
    {
        return $this->driver->getAdapter()->getPathPrefix().$path;
    }

    public function get(string $path): string
    {
        return $this->driver->read($path);
    }

    public function delete($paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (! $this->driver->delete($path)) {
                    $success = false;
                }
            } catch (FileNotFoundException $e) {
                $success = false;
            }
        }

        return $success;
    }

    public function move(string $from, string $to): string
    {
        return $this->driver->rename($from, $to);
    }

    public function size(string $path): int
    {
        return $this->driver->getSize($path);
    }

    public function mimeType(string $path)
    {
        return $this->driver->getMimetype($path);
    }

    public function lastModified(string $path): int
    {
        return $this->driver->getTimestamp($path);
    }

    public function url(string $path): string
    {
        $adapter = $this->driver->getAdapter();

        if (method_exists($adapter, 'getUrl')) {
            return $adapter->getUrl($path);
        } elseif (method_exists($this->driver, 'getUrl')) {
            return $this->driver->getUrl($path);
        } elseif ($adapter instanceof AwsS3Adapter) {
            return $this->getAwsUrl($adapter, $path);
        } elseif ($adapter instanceof LocalAdapter) {
            return $this->getLocalUrl($path);
        } else {
            throw new RuntimeException('This driver does not support retrieving URLs.');
        }
    }

    protected function getAwsUrl(AwsS3Adapter $adapter, string $path): string
    {
        return $adapter->getClient()->getObjectUrl(
            $adapter->getBucket(), $adapter->getPathPrefix().$path
        );
    }

    protected function getLocalUrl(string $path): string
    {
        return '/storage/'.$path;
    }

    public function makeDirectory(string $path): bool
    {
        return $this->driver->createDir($path);
    }

    public function deleteDirectory(string $directory): bool
    {
        return $this->driver->deleteDir($directory);
    }

    public function temporaryUrl($path, $expiration, array $options = [])
    {
        $adapter = $this->driver->getAdapter();

        if (method_exists($adapter, 'getTemporaryUrl')) {
            return $adapter->getTemporaryUrl($path, $expiration, $options);
        } elseif ($adapter instanceof AwsS3Adapter) {
            return $this->getAwsTemporaryUrl($adapter, $path, $expiration, $options);
        } else {
            throw new RuntimeException('This driver does not support creating temporary URLs.');
        }
    }

    public function getAwsTemporaryUrl($adapter, $path, $expiration, $options)
    {
        $client = $adapter->getClient();

        $command = $client->getCommand('GetObject', array_merge([
            'Bucket' => $adapter->getBucket(),
            'Key' => $adapter->getPathPrefix().$path,
        ], $options));

        return (string) $client->createPresignedRequest(
            $command, $expiration
        )->getUri();
    }

    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->driver, $method], $parameters);
    }
}