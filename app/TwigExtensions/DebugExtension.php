<?php

namespace App\TwigExtensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class DebugExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('dump', [$this, 'dump'])
        ];
    }

    public function dump($var)
    {
        dump($var);
    }
}
