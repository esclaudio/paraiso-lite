<?php

namespace App\TwigExtensions;

class VersionExtension extends \Twig_Extension
{
    /** @var string */
    private $manifestPath;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;

    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('version', [$this, 'version']),
        ];
    }

    public function version(string $path): string
    {
        if ( ! file_exists($this->manifestPath)) {
            return $path;
        }

        $manifest = json_decode(file_get_contents($this->manifestPath), true);

        return $manifest[$path] ?? $path;
    }
}
