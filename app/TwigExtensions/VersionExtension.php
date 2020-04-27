<?php

namespace App\TwigExtensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class VersionExtension extends AbstractExtension
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
            new TwigFunction('version', [$this, 'version']),
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
