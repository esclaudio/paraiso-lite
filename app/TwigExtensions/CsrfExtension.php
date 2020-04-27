<?php

namespace App\TwigExtensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Slim\Csrf\Guard;

class CsrfExtension extends AbstractExtension
{
    /**
     * @var Slim\Csrf\Guard
     */
    private $guard;

    public function __construct(Guard $guard)
    {
        $this->guard = $guard;

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_field', [$this, 'csrfField'], ['is_safe' => ['html']]),
        ];
    }

    public function csrfField(): string
    {
        return '
            <input type="hidden" name="'. $this->guard->getTokenNameKey() .'" value="'. $this->guard->getTokenName() .'">
            <input type="hidden" name="'. $this->guard->getTokenValueKey() .'" value="'. $this->guard->getTokenValue() .'">
        ';
    }
}
