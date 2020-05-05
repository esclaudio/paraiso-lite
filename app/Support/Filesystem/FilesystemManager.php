<?php

namespace App\Support\Filesystem;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\AbstractAdapter;
use InvalidArgumentException;
use Aws\S3\S3Client;
use App\Support\Filesystem\Filesystem;

class FilesystemManager
{
    protected $disks = [];

    public function disk(string $name): Filesystem
    {
        return $this->disks[$name];
    }

    public function add(string $name, array $properties): void
    {
        $adapterMethod = 'create'.ucfirst($properties['driver']).'Adapter';

        if (method_exists($this, $adapterMethod)) {
            $this->disks[$name] = new Filesystem($this->{$adapterMethod}($properties));
        } else {
            throw new InvalidArgumentException("Driver [{$properties['driver']}] is not supported.");
        }
    }

    private function createLocalAdapter(array $properties): AbstractAdapter
    {
        return new Local($properties['root']);
    }

    private function createS3Adapter(array $properties): AbstractAdapter
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => $properties['key'],
                'secret' => $properties['secret'],
            ],
            'region' => $properties['region'],
            'version' => 'latest',
        ]);

        return new AwsS3Adapter($client, $properties['bucket']);
    }
}
