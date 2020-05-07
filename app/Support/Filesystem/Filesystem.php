<?php

namespace App\Support\Filesystem;

use Slim\Http\UploadedFile;
use RuntimeException;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AdapterInterface;

/**
 * @mixin \League\Flysystem\FilesystemInterface
 */
class Filesystem
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $driver;

    public function __construct(AdapterInterface $adapter)
    {
        $this->driver = new LeagueFilesystem($adapter);
    }

    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string|resource  $contents
     * @param  mixed  $options
     * @return bool
     */
    public function put(string $path, $contents, $options = []): bool
    {
        $options = is_string($options)
            ? ['visibility' => $options]
            : (array) $options;

        if ($contents instanceof UploadedFile) {
            $stream = fopen($contents->file, 'r');
            $result = $this->put($path, $stream, $options);

            if (is_resource($stream)) {
                fclose($stream);
            }

            return $result;
        }

        return is_resource($contents)
            ? $this->driver->putStream($path, $contents, $options)
            : $this->driver->put($path, $contents, $options);
    }

    /**
     * Determine if a file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->driver->has($path);
    }

    /**
     * Get the full path for the file at the given "short" path.
     *
     * @param  string  $path
     * @return string
     */
    public function path(string $path)
    {
        return $this->driver->getAdapter()->getPathPrefix().$path;
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @return string
     */
    public function get(string $path): string
    {
        return $this->driver->read($path);
    }

    /**
     * Delete the file at a given path.
     *
     * @param  string|array  $paths
     * @return bool
     */
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

    /**
     * Copy a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy($from, $to)
    {
        return $this->driver->copy($from, $to);
    }

    /**
     * Move a file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function move(string $from, string $to): string
    {
        return $this->driver->rename($from, $to);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size(string $path): int
    {
        return $this->driver->getSize($path);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType(string $path)
    {
        return $this->driver->getMimetype($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified(string $path): int
    {
        return $this->driver->getTimestamp($path);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \RuntimeException
     */
    public function url(string $path): string
    {
        $adapter = $this->driver->getAdapter();

        if (method_exists($adapter, 'getUrl')) {
            return $adapter->getUrl($path);
        }

        if (method_exists($this->driver, 'getUrl')) {
            return $this->driver->getUrl($path);
        }

        if ($adapter instanceof AwsS3Adapter) {
            return $this->getAwsUrl($adapter, $path);
        }

        if ($adapter instanceof LocalAdapter) {
            return $this->getLocalUrl($path);
        }

        throw new RuntimeException('This driver does not support retrieving URLs.');
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        return $this->driver->readStream($path) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, array $options = [])
    {
        return $this->driver->writeStream($path, $resource, $options);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  \League\Flysystem\AwsS3v3\AwsS3Adapter  $adapter
     * @param  string  $path
     * @return string
     */
    protected function getAwsUrl(AwsS3Adapter $adapter, string $path): string
    {
        return $adapter->getClient()->getObjectUrl(
            $adapter->getBucket(), $adapter->getPathPrefix().$path
        );
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getLocalUrl(string $path): string
    {
        return '/storage/'.$path;
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function makeDirectory(string $path): bool
    {
        return $this->driver->createDir($path);
    }

    /**
     * Recursively delete a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory(string $directory): bool
    {
        return $this->driver->deleteDir($directory);
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public function temporaryUrl($path, $expiration, array $options = [])
    {
        $adapter = $this->driver->getAdapter();

        if (method_exists($adapter, 'getTemporaryUrl')) {
            return $adapter->getTemporaryUrl($path, $expiration, $options);
        }

        if ($adapter instanceof AwsS3Adapter) {
            return $this->getAwsTemporaryUrl($adapter, $path, $expiration, $options);
        }

        throw new RuntimeException('This driver does not support creating temporary URLs.');
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  \League\Flysystem\AwsS3v3\AwsS3Adapter  $adapter
     * @param  string $path
     * @param  \DateTimeInterface $expiration
     * @param  array $options
     * @return string
     */
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